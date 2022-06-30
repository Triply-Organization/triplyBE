<?php

namespace App\Service;

use App\Entity\Tour;
use App\Entity\TourImage;
use App\Entity\TourPlan;
use App\Mapper\TourRequestToTour;
use App\Repository\DestinationRepository;
use App\Repository\ImageRepository;
use App\Repository\ServiceRepository;
use App\Repository\TourImageRepository;
use App\Repository\TourPlanRepository;
use App\Repository\TourRepository;
use App\Request\TourRequest;

class TourService
{
    private TourRequestToTour $tourRequestToTour;
    private TourRepository $tourRepository;
    private ServiceRepository $serviceRepository;
    private DestinationRepository $destinationRepository;
    private TourPlanRepository $tourPlanRepository;
    private ImageRepository $imageRepository;
    private TourImageRepository $tourImageRepository;

    public function __construct(
        TourRequestToTour     $tourRequestToTour,
        TourRepository        $tourRepository,
        ServiceRepository     $serviceRepository,
        DestinationRepository $destinationRepository,
        TourPlanRepository    $tourPlanRepository,
        ImageRepository       $imageRepository,
        TourImageRepository   $tourImageRepository
    )
    {
        $this->tourRequestToTour = $tourRequestToTour;
        $this->tourRepository = $tourRepository;
        $this->serviceRepository = $serviceRepository;
        $this->destinationRepository = $destinationRepository;
        $this->tourPlanRepository = $tourPlanRepository;
        $this->imageRepository = $imageRepository;
        $this->tourImageRepository = $tourImageRepository;
    }

    public function addTour(TourRequest $tourRequest): Tour
    {
        $tourMapper = $this->tourRequestToTour->mapper($tourRequest);
        $tourImage = $this->addTourImage($tourRequest, $tourMapper);
        $tourService = $this->addServiceToTour($tourRequest, $tourImage);
        $tour = $this->addTourPlan($tourRequest, $tourService);
        $this->tourRepository->add($tour, true);
        return $tour;
    }

    private function addServiceToTour(TourRequest $tourRequest, Tour $tour): Tour
    {
        foreach ($tourRequest->getService() as $serviceRequest) {
            $service = $this->serviceRepository->find($serviceRequest);
            $tour->addService($service);
        }
        return $tour;
    }

    private function addTourPlan(TourRequest $tourRequest, Tour $tour): Tour
    {
        foreach ($tourRequest->getTourPlan() as $tourPlanRequest) {
            $destination = $this->destinationRepository->find($tourPlanRequest['destination']);
            if (is_object($destination)) {
                $tourPlan = new TourPlan();
                $tourPlan->setTitle($tourPlanRequest['title']);
                $tourPlan->setDescription($tourPlanRequest['description']);
                $tourPlan->setDay($tourPlanRequest['day']);
                $tourPlan->setDestination($destination);
                $tourPlan->setTour($tour);
                $this->tourPlanRepository->add($tourPlan);
            }
        }
        return $tour;
    }

    private function addTourImage(TourRequest $tourRequest, Tour $tour): Tour
    {
        foreach ($tourRequest->getTourImage() as $tourImageRequest) {
            $image = $this->imageRepository->find($tourImageRequest['id']);
            $tourImage = new TourImage();
            $tourImage->setType($tourImageRequest['type']);
            $tourImage->setTour($tour);
            $tourImage->setImage($image);
            $this->tourImageRepository->add($tourImage);
        }
        return $tour;
    }

    public function delete(Tour $tour):void
    {
        $this->tourPlanRepository->deleteWithRelation('tour', $tour->getId());

        $this->tourImageRepository->deleteWithRelation('tour', $tour->getId());

        $this->tourRepository->delete($tour->getId());
    }

    public function undoDelete(Tour $tour):void
    {
        $this->tourPlanRepository->undoDeleteWithRelation('tour', $tour->getId());

        $this->tourImageRepository->undoDeleteWithRelation('tour', $tour->getId());

        $this->tourRepository->undoDelete($tour->getId());
    }
}
