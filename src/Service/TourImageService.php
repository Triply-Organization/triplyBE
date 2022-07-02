<?php

namespace App\Service;

use App\Entity\Tour;
use App\Entity\TourImage;
use App\Repository\ImageRepository;
use App\Repository\TourImageRepository;
use App\Request\TourRequest;
use App\Request\TourUpdateRequest;
use App\Transformer\TourImageTransformer;

class TourImageService
{
    private ImageRepository $imageRepository;
    private TourImageRepository $tourImageRepository;
    private TourImageTransformer $tourImageTransformer;

    public function __construct(
        ImageRepository      $imageRepository,
        TourImageRepository  $tourImageRepository,
        TourImageTransformer $tourImageTransformer
    )
    {
        $this->imageRepository = $imageRepository;
        $this->tourImageRepository = $tourImageRepository;
        $this->tourImageTransformer = $tourImageTransformer;
    }

    public function getGallary(Tour $tour): array
    {
        $tourImages = $this->tourImageRepository->findBy(['tour' => $tour]);
        $gallery = [];
        foreach ($tourImages as $tourImage) {
            $gallery[] = $this->tourImageTransformer->toArray($tourImage);
        }

        return $gallery;
    }

    public function addTourImage(TourRequest $tourRequest, Tour $tour)
    {
        foreach ($tourRequest->getTourImages() as $tourImageRequest) {
            $image = $this->imageRepository->find($tourImageRequest['id']);
            if (!is_object($image)) {
                continue;
            }
            $tourImage = new TourImage();
            $tourImage->setType($tourImageRequest['type']);
            $tourImage->setTour($tour);
            $tourImage->setImage($image);
            $this->tourImageRepository->add($tourImage);
        }
    }

    public function updateTourImage(Tour $tour, TourUpdateRequest $tourUpdateRequest)
    {
        foreach ($tourUpdateRequest->getTourImages() as $tourImageRequest) {
            if (!$tourImageRequest['delete'] && !$tourImageRequest['type']) {
                continue;
            }
            if ($tourImageRequest['delete'] === true) {
                $this->deleteTourIamge($tourImageRequest);

                continue;
            }
            if ($tourImageRequest['type'] === "COVER" && $tourImageRequest['idTourImage']) {
                $this->addTourImageTypeCover($tourImageRequest);

                continue;
            }
            $this->addTourImageTypeGallery($tour, $tourImageRequest);
        }

        return $tour;
    }

    private function deleteTourIamge(array $tourImageRequest)
    {
        $tourImageDelete = $this->tourImageRepository->find($tourImageRequest['idTourImage']);
        if (!is_object($tourImageDelete)) {
            return;
        }
        $tourImageDelete->setDeletedAt(new \DateTimeImmutable());
        $this->tourImageRepository->add($tourImageDelete);
    }

    private function addTourImageTypeGallery(Tour $tour, array $tourImageRequest)
    {
        $image = $this->imageRepository->find($tourImageRequest['id']);
        if (!is_object($image)) {
            return $tour;
        }
        $tourImage = new TourImage();
        $tourImage->setType($tourImageRequest['type']);
        $tourImage->setTour($tour);
        $tourImage->setImage($image);
        $tourImage->setUpdatedAt(new \DateTimeImmutable());
        $this->tourImageRepository->add($tourImage);

        return $tour;
    }

    private function addTourImageTypeCover(array $tourImageRequest): void
    {
        $tourImage = $this->tourImageRepository->find($tourImageRequest['idTourImage']);
        if (!is_object($tourImage)) {
            return;
        }
        $image = $this->imageRepository->find($tourImageRequest['id']);
        if (!is_object($image)) {
            return;
        }
        $tourImage->setImage($image);
        $tourImage->setUpdatedAt(new \DateTimeImmutable());
        $this->tourImageRepository->add($tourImage);
    }
}
