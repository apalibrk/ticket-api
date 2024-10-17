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
        $organizer = $this->entityManager->getRepository(Organizer::class)->find($eventDTO->organizerId);

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

        $event->setTitle($eventDTO->title)
              ->setDate($eventDTO->date)
              ->setVenue($eventDTO->venue)
              ->setCapacity($eventDTO->capacity)
              ->setOrganizer($this->entityManager->getRepository(Organizer::class)->find($eventDTO->organizerId));

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
