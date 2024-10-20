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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/api/events')]
#[OA\Tag(name: 'Events')]
class EventController extends AbstractController
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    #[OA\Get(
        summary: 'List all events.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of all events',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Event::class, groups: ['full'])))
            )
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function getAllEvents(): JsonResponse
    {
        $events = $this->eventService->getAllEvents();
        return $this->json($events);
    }

    #[OA\Get(
        summary: 'Get details of a specific event.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the event'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the details of the event',
                content: new OA\JsonContent(ref: new Model(type: Event::class, groups: ['full']))
            ),
            new OA\Response(response: 404, description: 'Event not found')
        ]
    )]
    #[Route('/{id}', methods: ['GET'])]
    public function getEvent(int $id): JsonResponse
    {
        $event = $this->eventService->getEvent($id);
        if (!$event) {
            return $this->json(['message' => 'Event not found'], 404);
        }
        return $this->json($event);
    }

    #[OA\Post(
        summary: 'Create a new event.',
        requestBody: new OA\RequestBody(
            description: 'Event data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: EventDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Event created successfully',
                content: new OA\JsonContent(ref: new Model(type: Event::class))
            ),
            new OA\Response(response: 400, description: 'Invalid input')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
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

    #[OA\Put(
        summary: 'Update an existing event.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the event to update'
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated event data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: EventDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Event updated successfully',
                content: new OA\JsonContent(ref: new Model(type: Event::class))
            ),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 404, description: 'Event not found')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
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

    #[OA\Delete(
        summary: 'Delete an event.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the event to delete'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Event deleted successfully'),
            new OA\Response(response: 404, description: 'Event not found')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteEvent(int $id): JsonResponse
    {
        $this->eventService->deleteEvent($id);
        return $this->json(['message' => 'Event deleted']);
    }
}