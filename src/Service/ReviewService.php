<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Review;
use App\Entity\ReviewDetail;
use App\Entity\Tour;
use App\Entity\TypeReview;
use App\Repository\ReviewDetailRepository;
use App\Repository\ReviewRepository;
use App\Repository\TypeReviewRepository;
use App\Request\ReviewRequest;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;

use function Composer\Autoload\includeFile;

class ReviewService
{
    private Security $security;
    private OrderService $orderService;
    private ReviewRepository $reviewRepository;
    private TypeReviewRepository $typeReviewRepository;
    private ReviewDetailRepository $reviewDetailRepository;
    private ReviewDetailService $reviewDetailService;

    public function __construct(
        Security               $security,
        OrderService           $orderService,
        ReviewRepository       $reviewRepository,
        TypeReviewRepository   $typeReviewRepository,
        ReviewDetailRepository $reviewDetailRepository,
        ReviewDetailService    $reviewDetailService
    )
    {
        $this->security = $security;
        $this->orderService = $orderService;
        $this->reviewRepository = $reviewRepository;
        $this->typeReviewRepository = $typeReviewRepository;
        $this->reviewDetailRepository = $reviewDetailRepository;
        $this->reviewDetailService = $reviewDetailService;
    }

    public function handleRating(Tour $tour)
    {
        $reviews = $this->reviewRepository->findBy(['tour' => $tour]);
        $results = [];
        foreach ($reviews as $review) {
            $reviewDetails = $this->reviewDetailRepository->findBy(['review' => $review]);
            $typeRating = $this->reviewDetailService->getTypeRating($reviewDetails);
            $results[] = $typeRating;
        }

        return $results;
    }

    public function getRatingDetail(Tour $tour)
    {
        $results = [];
        $location = $rooms = $services = $price = $amenities = 0;
        $ratings = $this->handleRating($tour);
        $count = 0;
        foreach ($ratings as $rating) {
            if (count($rating) > 0) {
                $location = $location + $rating['location'];
                $rooms = $rooms + $rating['rooms'];
                $services = $services + $rating['services'];
                $price = $price + $rating['price'];
                $amenities = $amenities + $rating['amenities'];
                $count = $count + 1;
            }
        }
        if ($count > 0) {
            $results['location'] = $location / $count;
            $results['rooms'] = $rooms / $count;
            $results['price'] = $price / $count;
            $results['services'] = $services / $count;
            $results['amenities'] = $amenities / $count;
            $results['avg'] = ($location + $rooms + $services + $price) / (5 * $count);
        }

        return $results;
    }

    public function getRatingOverrall(Tour $tour)
    {
        $results = [];
        $location = $rooms = $services = $price = $amenities = 0;
        $ratings = $this->handleRating($tour);
        $count = 0;
        foreach ($ratings as $rating) {
            if (count($rating) > 0) {
                $location = $location + $rating['location'];
                $rooms = $rooms + $rating['rooms'];
                $services = $services + $rating['services'];
                $price = $price + $rating['price'];
                $amenities = $amenities + $rating['amenities'];
                $count = $count + 1;
            }
        }
        if ($count > 0) {
            $results['avg'] = ($location + $rooms + $services + $price) / (5 * $count);
        }

        return $results;
    }

    public function addReview(
        ReviewRequest $reviewRequest,
        Order         $order
    )
    {
        $currentUser = $this->security->getUser();
        $orderCommented = $this->reviewRepository->findBy(['orderDetail' => $order->getId()]);
        if ($currentUser->getId() !== $order->getUser()->getId() && $currentUser->getRoles()['role'] === 'ROLE_USER') {
            return false;
        }
        if ($orderCommented !== []) {
            return false;
        }
        $firstTicket = $this->orderService->findOneTicketOfOrder($order);
        $review = new Review();
        $review->setUser($currentUser)
            ->setTour($firstTicket->getPriceList()->getSchedule()->getTour())
            ->setOrderDetail($order)
            ->setComment($reviewRequest->getComment());
        $this->reviewRepository->add($review, true);
        $addReviewDetail = $this->addRate($reviewRequest, $review);
        if ($addReviewDetail === false) {
            return false;
        }

        return $review;
    }

    public function deleteReview(Review $review)
    {
        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $review->getUser()->getId() && $currentUser->getRoles()['role'] === 'ROLE_USER') {
            return false;
        }
        foreach ($review->getReviewDetails() as $reviewDetail) {
            $reviewDetail->setDeletedAt(new \DateTimeImmutable());
            $this->reviewDetailRepository->add($reviewDetail, true);
        }
        $review->setDeletedAt(new \DateTimeImmutable());
        $this->reviewRepository->add($review, true);

        return true;
    }

    public function getAllReviews(Tour $tour)
    {
        $reviews = $this->reviewRepository->findBy(['tour' => $tour]);
        $results = [];
        foreach ($reviews as $key => $review) {
            $reviewDetails = $this->reviewDetailRepository->findBy(['review' => $review]);
            $typeRatings = $this->reviewDetailService->getTypeRating($reviewDetails);
            $results[$key]['id'] = $review->getId();
            $results[$key]['name'] = $review->getUser()->getEmail();
            $results[$key]['createdAt'] = $review->getCreatedAt()->format('Y-m-d');
            $results[$key]['tourName'] = $review->getTour()->getTitle();
            $results[$key]['rating'] = $this->handleRatingUser($typeRatings);
            $results[$key]['avatar'] = is_null($review->getUser()->getAvatar())
                ? 'https://khajackie2206.s3.ap-southeast-1.amazonaws.com/upload/avataravatar-62c3a59886ab8.jpg'
                : $review->getUser()->getAvatar()->getPath();
            $results[$key]['comment'] = $review->getComment();
        }

        return $results;
    }

    public function handleRatingUser(array $typeRatings)
    {
        $results = [];
        if (count($typeRatings) > 0) {
            foreach ($typeRatings as $key => $typeRating) {
                $results[$key] = $typeRating;
            }
        }

        return [$results];
    }

    private function addRate(ReviewRequest $reviewRequest, Review $review)
    {
        $bool = true;
        foreach ($reviewRequest->getRate() as $rate) {
            $reviewDetail = new ReviewDetail();
            $typeResult = $this->typeReviewRepository->find($rate['id']);
            if ($typeResult === null) {
                $bool = false;
            }
            $reviewDetail->setRate($rate['rate'])
                ->setType($typeResult)
                ->setReview($review);
            $this->reviewDetailRepository->add($reviewDetail, true);
        }

        return $bool;
    }
}
