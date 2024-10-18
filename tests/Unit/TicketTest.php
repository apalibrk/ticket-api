<?php
namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\TicketService;
use App\DTO\TicketDTO;
use App\Entity\Ticket;
use App\Entity\Event;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class TicketServiceTest extends TestCase
{
    private $entityManager;
    private $ticketRepository;
    private $ticketService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->ticketRepository = $this->createMock(TicketRepository::class);
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Ticket::class, $this->ticketRepository],
                [Event::class, $this->createMock(EntityRepository::class)]
            ]);

        $this->ticketService = new TicketService($this->entityManager);
    }

    public function testCreateTicketWithEmptySeatNumber()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Seat number cannot be empty');

        $ticketDTO = $this->createMock(TicketDTO::class);
        $ticketDTO->method('getSeatNumber')->willReturn('');
        $ticketDTO->method('getPrice')->willReturn(100.0);
        $ticketDTO->method('getEventId')->willReturn(1);

        $this->ticketService->createTicket($ticketDTO);
    }

    public function testCreateTicketWithNegativePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ticket price cannot be negative');

        $ticketDTO = $this->createMock(TicketDTO::class);
        $ticketDTO->method('getSeatNumber')->willReturn('A1');
        $ticketDTO->method('getPrice')->willReturn(-100.0);
        $ticketDTO->method('getEventId')->willReturn(1);

        $this->ticketService->createTicket($ticketDTO);
    }

    public function testCreateTicketWithNonExistentEvent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event not found');

        $ticketDTO = $this->createMock(TicketDTO::class);
        $ticketDTO->method('getSeatNumber')->willReturn('A1');
        $ticketDTO->method('getPrice')->willReturn(100.0);
        $ticketDTO->method('getEventId')->willReturn(1);

        $eventRepository = $this->createMock(EntityRepository::class);
        $eventRepository->method('find')->willReturn(null);
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Ticket::class, $this->ticketRepository],
                [Event::class, $eventRepository]
            ]);

        $this->ticketService->createTicket($ticketDTO);
    }

    public function testUpdateTicketWithEmptySeatNumber()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Seat number cannot be empty');

        $ticket = $this->createMock(Ticket::class);
        $ticketDTO = $this->createMock(TicketDTO::class);
        $ticketDTO->method('getSeatNumber')->willReturn('');
        $ticketDTO->method('getPrice')->willReturn(100.0);
        $ticketDTO->method('getStatus')->willReturn('available');

        $this->ticketService->updateTicket($ticket, $ticketDTO);
    }

    public function testUpdateTicketWithNegativePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ticket price cannot be negative');

        $ticket = $this->createMock(Ticket::class);
        $ticketDTO = $this->createMock(TicketDTO::class);
        $ticketDTO->method('getSeatNumber')->willReturn('A1');
        $ticketDTO->method('getPrice')->willReturn(-100.0);
        $ticketDTO->method('getStatus')->willReturn('available');

        $this->ticketService->updateTicket($ticket, $ticketDTO);
    }

    public function testUpdateTicketSuccessfully()
    {
        $ticket = $this->createMock(Ticket::class);
        $ticketDTO = $this->createMock(TicketDTO::class);
        $ticketDTO->method('getSeatNumber')->willReturn('A1');
        $ticketDTO->method('getPrice')->willReturn(100.0);
        $ticketDTO->method('getStatus')->willReturn('available');

        $ticket->expects($this->once())->method('setSeatNumber')->with('A1');
        $ticket->expects($this->once())->method('setPrice')->with(100.0);
        $ticket->expects($this->once())->method('setStatus')->with('available');

        $this->entityManager->expects($this->once())->method('flush');

        $updatedTicket = $this->ticketService->updateTicket($ticket, $ticketDTO);

        $this->assertSame($ticket, $updatedTicket);
    }

    public function testMarkAsSold()
    {
        $ticket = $this->createMock(Ticket::class);
        $ticket->expects($this->once())->method('setStatus')->with('sold');

        $this->entityManager->expects($this->once())->method('flush');

        $updatedTicket = $this->ticketService->markAsSold($ticket);

        $this->assertSame($ticket, $updatedTicket);
    }

    public function testDeleteTicket()
    {
        $ticket = $this->createMock(Ticket::class);

        $this->entityManager->expects($this->once())->method('remove')->with($ticket);
        $this->entityManager->expects($this->once())->method('flush');

        $this->ticketService->deleteTicket($ticket);
    }

    public function testFindAll()
    {
        $tickets = [$this->createMock(Ticket::class)];

        $this->ticketRepository->method('findAll')->willReturn($tickets);

        $result = $this->ticketService->findAll();

        $this->assertSame($tickets, $result);
    }
}