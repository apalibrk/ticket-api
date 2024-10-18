<?php

namespace App\Service;

use App\DTO\EventDTO;
use App\Entity\Event;
use App\Entity\Organizer;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventService
{
    private EventRepository $eventRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
    }

    public function createEvent(EventDTO $eventDTO): Event
    {
        if ($eventDTO->date < new \DateTime()) {
            throw new \InvalidArgumentException('Event date cannot be in the past');
        }
    
        if (empty($eventDTO->title)) {
            throw new \InvalidArgumentException('Event title cannot be empty');
        }
    
        if (empty($eventDTO->venue)) {
            throw new \InvalidArgumentException('Event venue cannot be empty');
        }
    
        if ($eventDTO->capacity <= 0) {
            throw new \InvalidArgumentException('Event capacity must be greater than zero');
        }
    
        $organizer = $this->entityManager->getRepository(Organizer::class)->find($eventDTO->organizerId);
        if (!$organizer) {
            throw new \InvalidArgumentException('Organizer not found');
        }
    
        $event = new Event();
        $event->setTitle($eventDTO->title)
              ->setDate($eventDTO->date)
              ->setVenue($eventDTO->venue)
              ->setCapacity($eventDTO->capacity)
              ->setOrganizer($organizer);
    
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    
        return $event;
    }
    
    public function updateEvent(int $id, EventDTO $eventDTO): Event
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new \Exception('Event not found');
        }

        if ($eventDTO->date < new \DateTime()) {
            throw new \InvalidArgumentException('Event date cannot be in the past');
        }

        if (empty($eventDTO->title)) {
            throw new \InvalidArgumentException('Event title cannot be empty');
        }

        if (empty($eventDTO->venue)) {
            throw new \InvalidArgumentException('Event venue cannot be empty');
        }

        if ($eventDTO->capacity <= 0) {
            throw new \InvalidArgumentException('Event capacity must be greater than zero');
        }

        // Validate the organizer
        $organizer = $this->entityManager->getRepository(Organizer::class)->find($eventDTO->organizerId);
        if (!$organizer) {
            throw new \InvalidArgumentException('Organizer not found');
        }

        // Update the Event object
        $event->setTitle($eventDTO->title)
            ->setDate($eventDTO->date)
            ->setVenue($eventDTO->venue)
            ->setCapacity($eventDTO->capacity)
            ->setOrganizer($organizer);

        $this->entityManager->flush();

        return $event;
    }

    public function deleteEvent(int $id): void
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new \Exception('Event not found');
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }

    public function getEvent(int $id): ?Event
    {
        return $this->eventRepository->find($id);
    }

    public function getAllEvents(): array
    {
        return $this->eventRepository->findAll();
    }
}
