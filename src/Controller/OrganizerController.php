<?php
// src/Controller/OrganizerController.php

namespace App\Controller;

use App\DTO\OrganizerDTO;
use App\Entity\Organizer;
use App\Service\OrganizerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrganizerController extends AbstractController
{
    private $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    /**
     * @Route("/api/organizers", methods={"POST"})
     */
    public function createOrganizer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $organizerDTO = new OrganizerDTO();
        $organizerDTO->setName($data['name']);
        $organizerDTO->setEmail($data['email']);
        $organizerDTO->setPhone($data['phone']);
        $organizerDTO->setPassword($data['password']); // Consider hashing the password

        $organizer = $this->organizerService->createOrganizer($organizerDTO);
        return $this->json($organizer, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/organizers/{id}", methods={"PUT"})
     */
    public function updateOrganizer(Request $request, Organizer $organizer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $organizerDTO = new OrganizerDTO();
        $organizerDTO->setName($data['name']);
        $organizerDTO->setEmail($data['email']);
        $organizerDTO->setPhone($data['phone']);
        $organizerDTO->setPassword($data['password']); // Consider hashing the password

        $updatedOrganizer = $this->organizerService->updateOrganizer($organizer, $organizerDTO);
        return $this->json($updatedOrganizer);
    }

    /**
     * @Route("/api/organizers/{id}", methods={"DELETE"})
     */
    public function deleteOrganizer(Organizer $organizer): JsonResponse
    {
        $this->organizerService->deleteOrganizer($organizer);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/organizers", methods={"GET"})
     */
    public function listOrganizers(): JsonResponse
    {
        $organizers = $this->organizerService->findAll();
        return $this->json($organizers);
    }
}
