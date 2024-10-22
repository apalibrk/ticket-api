<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Ticket;
use App\Entity\Event;
use App\Entity\Organizer;
use Doctrine\ORM\EntityManagerInterface;

class TicketControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function testCreateTicket()
    {
        // Create an event for the ticket
        // $event = new Event();
        // $event->setTitle('Sample Event');
        // $event->setDate(new \DateTime('2023-01-01'));
        // $event->setVenue('Sample Venue');
        // $event->setCapacity(100);
        // $organizer = $this->entityManager->getRepository(Organizer::class)->findOneBy([]);
        // $event->setOrganizer($organizer);

        $event = $this->entityManager->getRepository(Event::class)->findOneBy([]);

        // $this->entityManager->persist($event);
        // $this->entityManager->flush();

        $this->client->request('POST', '/api/tickets', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'seatNumber' => 'A1',
            'price' => 99.99,
            'status' => 'available',
            'eventId' => $event->getId()
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['seatNumber' => 'A1']);
        $this->assertNotNull($ticket);
        $this->assertEquals('A1', $ticket->getSeatNumber());
    }

    public function testUpdateTicket()
    {
        // Create an event for the ticket
        $event = new Event();
        $event->setTitle('Sample Event');
        $event->setDate(new \DateTime('2023-01-01'));
        $event->setVenue('Sample Venue');
        $event->setCapacity(100);
        $organizer = $this->entityManager->getRepository(Organizer::class)->findOneBy([]);
        $event->setOrganizer($organizer);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // First, create a ticket
        $ticket = new Ticket();
        $ticket->setSeatNumber('A1');
        $ticket->setPrice(99.99);
        $ticket->setStatus('available');
        $ticket->setEvent($event);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        // Then, update the ticket
        $this->client->request('PUT', '/api/tickets/' . $ticket->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'seatNumber' => 'A2',
            'price' => 120.00,
            'status' => 'sold',
            'event' => $event->getId()
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $updatedTicket = $this->entityManager->getRepository(Ticket::class)->find($ticket->getId());
        $this->assertEquals('A2', $updatedTicket->getSeatNumber());
        $this->assertEquals(120.00, $updatedTicket->getPrice());
        $this->assertEquals('sold', $updatedTicket->getStatus());
    }
}