<?php

namespace App\Controller;

use App\DTO\TicketDTO;
use App\Entity\Ticket;
use App\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[Route('/api/tickets')]
#[OA\Tag(name: "tickets")]
class TicketController extends AbstractController
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Create a new ticket.
     *
     * @OA\RequestBody(
     *     description="Ticket data",
     *     @OA\JsonContent(ref=@Model(type=TicketDTO::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Ticket created",
     *     @OA\JsonContent(ref=@Model(type=Ticket::class))
     * )
     */
    #[Route('', methods: ['POST'])]
    public function createTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketDTO = new TicketDTO();
        $ticketDTO->setSeatNumber($data['seatNumber']);
        $ticketDTO->setPrice($data['price']);
        $ticketDTO->setStatus('available'); // Default status
        $ticketDTO->setEventId($data['eventId']);

        $ticket = $this->ticketService->createTicket($ticketDTO);
        return $this->json($ticket, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update an existing ticket.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the ticket to update",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\RequestBody(
     *     description="Updated ticket data",
     *     @OA\JsonContent(ref=@Model(type=TicketDTO::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Ticket updated",
     *     @OA\JsonContent(ref=@Model(type=Ticket::class))
     * )
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function updateTicket(Request $request, Ticket $ticket): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketDTO = new TicketDTO();
        $ticketDTO->setSeatNumber($data['seatNumber']);
        $ticketDTO->setPrice($data['price']);
        $ticketDTO->setStatus($data['status']);

        $updatedTicket = $this->ticketService->updateTicket($ticket, $ticketDTO);
        return $this->json($updatedTicket);
    }

    /**
     * Mark a ticket as sold.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the ticket to mark as sold",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Ticket marked as sold",
     *     @OA\JsonContent(ref=@Model(type=Ticket::class))
     * )
     */
    #[Route('/{id}/sell', methods: ['PUT'])]
    public function sellTicket(Ticket $ticket): JsonResponse
    {
        $soldTicket = $this->ticketService->markAsSold($ticket);
        return $this->json($soldTicket);
    }

    /**
     * Delete a ticket.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the ticket to delete",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=204,
     *     description="Ticket deleted"
     * )
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteTicket(Ticket $ticket): JsonResponse
    {
        $this->ticketService->deleteTicket($ticket);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * List all tickets.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of all tickets",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Ticket::class, groups={"full"}))
     *     )
     * )
     */
    #[Route('', methods: ['GET'])]
    public function listTickets(): JsonResponse
    {
        $tickets = $this->ticketService->findAll();
        return $this->json($tickets);
    }
}