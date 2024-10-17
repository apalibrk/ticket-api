<?php

namespace App\Controller;

use App\DTO\EventDTO;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/events')]
class EventController extends AbstractController
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    #[Route('', methods: ['GET'])]
    public function getAllEvents(): JsonResponse
    {
        $events = $this->eventService->getAllEvents();
        return $this->json($events);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getEvent(int $id): JsonResponse
    {
        $event = $this->eventService->getEvent($id);
        if (!$event) {
            return $this->json(['message' => 'Event not found'], 404);
        }
        return $this->json($event);
    }

    #[Route('', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $eventDTO = new EventDTO(
            null,
            $data['title'],
            new \DateTime($data['date']),
            $data['venue'],
            $data['capacity'],
            $data['organizerId']
        );

        $event = $this->eventService->createEvent($eventDTO);
        return $this->json($event, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function updateEvent(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $eventDTO = new EventDTO(
            $id,
            $data['title'],
            new \DateTime($data['date']),
            $data['venue'],
            $data['capacity'],
            $data['organizerId']
        );

        $event = $this->eventService->updateEvent($id, $eventDTO);
        return $this->json($event);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteEvent(int $id): JsonResponse
    {
        $this->eventService->deleteEvent($id);
        return $this->json(['message' => 'Event deleted']);
    }
}
