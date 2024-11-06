<?php

namespace App\Controller;

use App\DTO\TicketDTO;
use App\Entity\Ticket;
use App\Entity\Event;
use App\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tickets')]
#[OA\Tag(name: 'Tickets')]
class TicketController extends AbstractController
{
    private TicketService $ticketService;
    private ValidatorInterface $validator;

    public function __construct(TicketService $ticketService, ValidatorInterface $validator)
    {
        $this->ticketService = $ticketService;
        $this->validator = $validator;
    }

    #[OA\Post(
        summary: 'Create a new ticket.',
        requestBody: new OA\RequestBody(
            description: 'Ticket data',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'seatNumber', type: 'string', example: 'A1', description: 'Seat number of the ticket'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 50.0, description: 'Price of the ticket'),
                    new OA\Property(property: 'status', type: 'string', example: 'available', description: 'Status of the ticket'),
                    new OA\Property(property: 'eventId', type: 'integer', example: 1, description: 'ID of the event associated with the ticket')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Ticket created successfully',
                content: new OA\JsonContent(ref: new Model(type: Ticket::class, groups: ['full']))
            ),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Internal server error')
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
    public function createTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $ticketDTO = new TicketDTO();
        $ticketDTO->setSeatNumber($data['seatNumber'] ?? '');
        $ticketDTO->setPrice($data['price'] ?? 0);
        $ticketDTO->setStatus($data['status'] ?? 'available');
        $ticketDTO->setEventId($data['eventId'] ?? 0);

        $errors = $this->validator->validate($ticketDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $ticket = $this->ticketService->createTicket($ticketDTO);
        return $this->json($ticket, JsonResponse::HTTP_CREATED);
    }

    #[OA\Put(
        summary: 'Update an existing ticket.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the ticket to update'
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated ticket data',
            content: new OA\JsonContent(ref: new Model(type: TicketDTO::class))
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ticket updated successfully', content: new OA\JsonContent(ref: new Model(type: Ticket::class, groups: ['full']))),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 404, description: 'Ticket not found')
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
    #[ParamConverter('ticket', class: Ticket::class)]
    public function updateTicket(Request $request, Ticket $ticket): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $ticketDTO = new TicketDTO();
        $ticketDTO->setSeatNumber($data['seatNumber'] ?? $ticket->getSeatNumber());
        $ticketDTO->setPrice($data['price'] ?? $ticket->getPrice());
        $ticketDTO->setStatus($data['status'] ?? $ticket->getStatus());
        $ticketDTO->setEventId($ticket->getEvent()->getId());

        $errors = $this->validator->validate($ticketDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $updatedTicket = $this->ticketService->updateTicket($ticket, $ticketDTO);
        return $this->json($updatedTicket);
    }

    #[OA\Put(
        summary: 'Mark a ticket as sold.',
        path: '/api/tickets/{id}/sell',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the ticket to mark as sold'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ticket marked as sold', content: new OA\JsonContent(ref: new Model(type: Ticket::class, groups: ['full']))),
            new OA\Response(response: 404, description: 'Ticket not found')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
    #[Route('/{id}/sell', methods: ['PUT'])]
    #[ParamConverter('ticket', class: Ticket::class)]
    public function sellTicket(Ticket $ticket): JsonResponse
    {
        $soldTicket = $this->ticketService->markAsSold($ticket);
        return $this->json($soldTicket);
    }

    #[OA\Delete(
        summary: 'Delete a ticket.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the ticket to delete'
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Ticket deleted successfully'),
            new OA\Response(response: 404, description: 'Ticket not found')
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
    #[ParamConverter('ticket', class: Ticket::class)]
    public function deleteTicket(Ticket $ticket): JsonResponse
    {
        $this->ticketService->deleteTicket($ticket);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[OA\Get(
        summary: 'List all tickets.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of all tickets',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: '#/components/schemas/Ticket',
                        example: [
                            'id' => 1,
                            'seatNumber' => 'A1',
                            'price' => 50.0,
                            'status' => 'available',
                            'event' => 1
                        ]
                    )
                )
            )
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function listTickets(): JsonResponse
    {
        $tickets = $this->ticketService->findAll();
        return $this->json($tickets);
    }
}