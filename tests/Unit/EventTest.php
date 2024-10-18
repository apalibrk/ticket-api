<?php
namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\EventService;
use App\DTO\EventDTO;
use App\Entity\Event;
use App\Entity\Organizer;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class EventServiceTest extends TestCase
{
    private $entityManager;
    private $eventRepository;
    private $eventService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Event::class, $this->eventRepository],
                [Organizer::class, $this->createMock(EntityRepository::class)]
            ]);

        $this->eventService = new EventService($this->eventRepository, $this->entityManager);
    }

    public function testCreateEventWithPastDate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event date cannot be in the past');

        $eventDTO = $this->createMock(EventDTO::class);
        $eventDTO->date = new \DateTime('-1 day');
        $eventDTO->title = 'Event Title';
        $eventDTO->venue = 'Event Venue';
        $eventDTO->capacity = 100;
        $eventDTO->organizerId = 1;

        $this->eventService->createEvent($eventDTO);
    }

    public function testCreateEventWithEmptyTitle()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event title cannot be empty');

        $eventDTO = $this->createMock(EventDTO::class);
        $eventDTO->date = new \DateTime('+1 day');
        $eventDTO->title = '';
        $eventDTO->venue = 'Event Venue';
        $eventDTO->capacity = 100;
        $eventDTO->organizerId = 1;

        $this->eventService->createEvent($eventDTO);
    }

    public function testCreateEventWithEmptyVenue()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event venue cannot be empty');

        $eventDTO = $this->createMock(EventDTO::class);
        $eventDTO->date = new \DateTime('+1 day');
        $eventDTO->title = 'Event Title';
        $eventDTO->venue = '';
        $eventDTO->capacity = 100;
        $eventDTO->organizerId = 1;

        $this->eventService->createEvent($eventDTO);
    }

    public function testCreateEventWithZeroCapacity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event capacity must be greater than zero');

        $eventDTO = $this->createMock(EventDTO::class);
        $eventDTO->date = new \DateTime('+1 day');
        $eventDTO->title = 'Event Title';
        $eventDTO->venue = 'Event Venue';
        $eventDTO->capacity = 0;
        $eventDTO->organizerId = 1;

        $this->eventService->createEvent($eventDTO);
    }

    public function testCreateEventWithNonExistentOrganizer()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Organizer not found');

        $eventDTO = $this->createMock(EventDTO::class);
        $eventDTO->date = new \DateTime('+1 day');
        $eventDTO->title = 'Event Title';
        $eventDTO->venue = 'Event Venue';
        $eventDTO->capacity = 100;
        $eventDTO->organizerId = 1;

        $organizerRepository = $this->createMock(EntityRepository::class);
        $organizerRepository->method('find')->willReturn(null);
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Event::class, $this->eventRepository],
                [Organizer::class, $organizerRepository]
            ]);

        $this->eventService->createEvent($eventDTO);
    }

   

    public function testUpdateEventWithNonExistentEvent()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Event not found');

        $eventDTO = $this->createMock(EventDTO::class);
        $eventDTO->date = new \DateTime('+1 day');
        $eventDTO->title = 'Event Title';
        $eventDTO->venue = 'Event Venue';
        $eventDTO->capacity = 100;
        $eventDTO->organizerId = 1;

        $this->eventRepository->method('find')->willReturn(null);

        $this->eventService->updateEvent(1, $eventDTO);
    }
    
    public function testDeleteEventWithNonExistentEvent()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Event not found');

        $this->eventRepository->method('find')->willReturn(null);

        $this->eventService->deleteEvent(1);
    }

    public function testDeleteEventSuccessfully()
    {
        $event = $this->createMock(Event::class);

        $this->eventRepository->method('find')->willReturn($event);

        $this->entityManager->expects($this->once())->method('remove')->with($event);
        $this->entityManager->expects($this->once())->method('flush');

        $this->eventService->deleteEvent(1);
    }

    public function testGetEvent()
    {
        $event = $this->createMock(Event::class);

        $this->eventRepository->method('find')->willReturn($event);

        $result = $this->eventService->getEvent(1);

        $this->assertSame($event, $result);
    }

    public function testGetAllEvents()
    {
        $events = [$this->createMock(Event::class)];

        $this->eventRepository->method('findAll')->willReturn($events);

        $result = $this->eventService->getAllEvents();

        $this->assertSame($events, $result);
    }
}