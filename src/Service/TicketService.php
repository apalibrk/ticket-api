<?php

namespace App\Service;

use App\Entity\Ticket;
use App\DTO\TicketDTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TicketRepository;

class TicketService
{
    private $entityManager;
    private $ticketRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->ticketRepository = $entityManager->getRepository(Ticket::class);
    }

    public function createTicket(TicketDTO $ticketDTO): Ticket
    {
        $ticket = new Ticket();
        $ticket->setSeatNumber($ticketDTO->getSeatNumber());
        $ticket->setPrice($ticketDTO->getPrice());
        $ticket->setStatus('available'); // Default status

        // Set the Event based on eventId from DTO
        $event = $this->entityManager->getRepository(Event::class)->find($ticketDTO->getEventId());
        if (!$event) {
            throw new \Exception("Event not found");
        }
        $ticket->setEvent($event);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $ticket;
    }

    public function updateTicket(Ticket $ticket, TicketDTO $ticketDTO): Ticket
    {
        $ticket->setSeatNumber($ticketDTO->getSeatNumber());
        $ticket->setPrice($ticketDTO->getPrice());
        $ticket->setStatus($ticketDTO->getStatus());

        $this->entityManager->flush();

        return $ticket;
    }

    public function markAsSold(Ticket $ticket): Ticket
    {
        $ticket->setStatus('sold');
        $this->entityManager->flush();

        return $ticket;
    }

    public function deleteTicket(Ticket $ticket): void
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->ticketRepository->findAll();
    }
}
