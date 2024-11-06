<?php

namespace App\Service;

use App\Entity\Ticket;
use App\DTO\TicketDTO;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TicketRepository;

/**
 * Service class for managing tickets.
 */
class TicketService
{
    private EntityManagerInterface $entityManager;
    private TicketRepository $ticketRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->ticketRepository = $entityManager->getRepository(Ticket::class);
    }

    /**
     * Creates a new ticket.
     *
     * @param TicketDTO $ticketDTO The data transfer object containing ticket details.
     * @return Ticket The created ticket.
     * @throws \InvalidArgumentException If the ticket data is invalid.
     */
    public function createTicket(TicketDTO $ticketDTO): Ticket
    {
        $status = 'available';

        $event = $this->entityManager->getRepository(Event::class)->find($ticketDTO->getEventId());
        if (!$event) {
            throw new \InvalidArgumentException('Event not found');
        }

        $ticket = new Ticket();
        $ticket->setSeatNumber($ticketDTO->getSeatNumber());
        $ticket->setPrice($ticketDTO->getPrice());
        $ticket->setStatus($status);
        $ticket->setEvent($event);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $ticket;
    }

    /**
     * Updates an existing ticket.
     *
     * @param Ticket $ticket The ticket to update.
     * @param TicketDTO $ticketDTO The data transfer object containing updated ticket details.
     * @return Ticket The updated ticket.
     * @throws \InvalidArgumentException If the ticket data is invalid.
     */
    public function updateTicket(Ticket $ticket, TicketDTO $ticketDTO): Ticket
    {
        $ticket->setSeatNumber($ticketDTO->getSeatNumber());
        $ticket->setPrice($ticketDTO->getPrice());
        $ticket->setStatus($ticketDTO->getStatus());

        $this->entityManager->flush();

        return $ticket;
    }

    /**
     * Marks a ticket as sold.
     *
     * @param Ticket $ticket The ticket to mark as sold.
     * @return Ticket The updated ticket.
     */
    public function markAsSold(Ticket $ticket): Ticket
    {
        $ticket->setStatus('sold');
        $this->entityManager->flush();

        return $ticket;
    }

    /**
     * Deletes a ticket.
     *
     * @param Ticket $ticket The ticket to delete.
     */
    public function deleteTicket(Ticket $ticket): void
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();
    }

    /**
     * Retrieves all tickets.
     *
     * @return Ticket[] An array of all tickets.
     */
    public function findAll(): array
    {
        return $this->ticketRepository->findAll();
    }
}