<?php
// src/Controller/TicketController.php

namespace App\Controller;

use App\DTO\TicketDTO;
use App\Entity\Ticket;
use App\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    private $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * @Route("/api/tickets", methods={"POST"})
     */
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
     * @Route("/api/tickets/{id}", methods={"PUT"})
     */
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
     * @Route("/api/tickets/{id}/sell", methods={"PUT"})
     */
    public function sellTicket(Ticket $ticket): JsonResponse
    {
        $soldTicket = $this->ticketService->markAsSold($ticket);
        return $this->json($soldTicket);
    }

    /**
     * @Route("/api/tickets/{id}", methods={"DELETE"})
     */
    public function deleteTicket(Ticket $ticket): JsonResponse
    {
        $this->ticketService->deleteTicket($ticket);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/tickets", methods={"GET"})
     */
    public function listTickets(): JsonResponse
    {
        $tickets = $this->ticketService->findAll();
        return $this->json($tickets);
    }
}
