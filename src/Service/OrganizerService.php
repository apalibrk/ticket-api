<?php
// src/Service/OrganizerService.php

namespace App\Service;

use App\Entity\Organizer;
use App\DTO\OrganizerDTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizerRepository;

class OrganizerService
{
    private $entityManager;
    private $organizerRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->organizerRepository = $entityManager->getRepository(Organizer::class);
    }

    public function createOrganizer(OrganizerDTO $organizerDTO): Organizer
    {
        $organizer = new Organizer();
        $organizer->setName($organizerDTO->getName());
        $organizer->setEmail($organizerDTO->getEmail());
        $organizer->setPhone($organizerDTO->getPhone());
        $organizer->setPassword($organizerDTO->getPassword()); // Consider hashing the password

        $this->entityManager->persist($organizer);
        $this->entityManager->flush();

        return $organizer;
    }

    public function updateOrganizer(Organizer $organizer, OrganizerDTO $organizerDTO): Organizer
    {
        $organizer->setName($organizerDTO->getName());
        $organizer->setEmail($organizerDTO->getEmail());
        $organizer->setPhone($organizerDTO->getPhone());
        $organizer->setPassword($organizerDTO->getPassword()); // Consider hashing the password

        $this->entityManager->flush();

        return $organizer;
    }

    public function deleteOrganizer(Organizer $organizer): void
    {
        $this->entityManager->remove($organizer);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->organizerRepository->findAll();
    }
}
