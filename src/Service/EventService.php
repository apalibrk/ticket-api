<?php

namespace App\Service;

use App\DTO\EventDTO;
use App\Entity\Event;
use App\Entity\Organizer;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service class for managing events.
 */
class EventService
{
    private EventRepository $eventRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Creates a new event.
     *
     * @param EventDTO $eventDTO The data transfer object containing event details.
     * @return Event The created event.
     * @throws \InvalidArgumentException If the event data is invalid.
     */
    public function createEvent(EventDTO $eventDTO): Event
    {
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

    /**
     * Updates an existing event.
     *
     * @param int $id The ID of the event to update.
     * @param EventDTO $eventDTO The data transfer object containing updated event details.
     * @return Event The updated event.
     * @throws \Exception If the event is not found.
     * @throws \InvalidArgumentException If the event data is invalid.
     */
    public function updateEvent(int $id, EventDTO $eventDTO): Event
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new \Exception('Event not found');
        }

        if ($eventDTO->date < new \DateTime()) {
            throw new \InvalidArgumentException('Event date cannot be in the past');
        }

        $organizer = $this->entityManager->getRepository(Organizer::class)->find($eventDTO->organizerId);
        if (!$organizer) {
            throw new \InvalidArgumentException('Organizer not found');
        }

        $event->setTitle($eventDTO->title)
              ->setDate($eventDTO->date)
              ->setVenue($eventDTO->venue)
              ->setCapacity($eventDTO->capacity)
              ->setOrganizer($organizer);

        $this->entityManager->flush();

        return $event;
    }

    /**
     * Deletes an event.
     *
     * @param int $id The ID of the event to delete.
     * @throws \Exception If the event is not found.
     */
    public function deleteEvent(int $id): void
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            throw new \Exception('Event not found');
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }

    /**
     * Retrieves an event by its ID.
     *
     * @param int $id The ID of the event to retrieve.
     * @return Event|null The event, or null if not found.
     */
    public function getEvent(int $id): ?Event
    {
        return $this->eventRepository->find($id);
    }

    /**
     * Retrieves all events.
     *
     * @return Event[] An array of all events.
     */
    public function getAllEvents(): array
    {
        return $this->eventRepository->findAll();
    }
}