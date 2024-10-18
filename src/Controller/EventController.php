<?php

namespace App\Controller;

use App\DTO\EventDTO;
use App\Entity\Event;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[Route('/api/events')]
#[OA\Tag(name: "events")]
class EventController extends AbstractController
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * List all events.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of all events",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Event::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="events")
     */
    #[Route('', methods: ['GET'])]
    public function getAllEvents(): JsonResponse
    {
        $events = $this->eventService->getAllEvents();
        return $this->json($events);
    }

    /**
     * Get details of a specific event.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the event",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the details of the event",
     *     @OA\JsonContent(ref=@Model(type=Event::class, groups={"full"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Event not found"
     * )
     */
    #[Route('/{id}', methods: ['GET'])]
    public function getEvent(int $id): JsonResponse
    {
        $event = $this->eventService->getEvent($id);
        if (!$event) {
            return $this->json(['message' => 'Event not found'], 404);
        }
        return $this->json($event);
    }

    /**
     * Create a new event.
     *
     * @OA\RequestBody(
     *     description="Event data",
     *     @OA\JsonContent(ref=@Model(type=EventDTO::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Event created",
     *     @OA\JsonContent(ref=@Model(type=Event::class))
     * )
     */
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

    /**
     * Update an existing event.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the event to update",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\RequestBody(
     *     description="Updated event data",
     *     @OA\JsonContent(ref=@Model(type=EventDTO::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Event updated",
     *     @OA\JsonContent(ref=@Model(type=Event::class))
     * )
     */
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

    /**
     * Delete an event.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the event to delete",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Event deleted"
     * )
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteEvent(int $id): JsonResponse
    {
        $this->eventService->deleteEvent($id);
        return $this->json(['message' => 'Event deleted']);
    }
}