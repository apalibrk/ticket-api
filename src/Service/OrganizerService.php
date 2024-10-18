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
        if (empty($organizerDTO->getName())) {
            throw new \InvalidArgumentException('Organizer name cannot be empty');
        }
    
        if (!filter_var($organizerDTO->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    
        if (!preg_match('/^\+?[0-9]{1,4}?[-. ]?(\(?[0-9]{1,4}?\)?[-. ]?)?[0-9]{1,4}[-. ]?[0-9]{1,9}$/', $organizerDTO->getPhone())) {
            throw new \InvalidArgumentException('Invalid phone number format');
        }
    
        if (strlen($organizerDTO->getPassword()) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long');
        }
    
        $organizer = new Organizer();
        $organizer->setName($organizerDTO->getName());
        $organizer->setEmail($organizerDTO->getEmail());
        $organizer->setPhone($organizerDTO->getPhone());
        $organizer->setPassword($organizerDTO->getPassword());
    
        $this->entityManager->persist($organizer);
        $this->entityManager->flush();
    
        return $organizer;
    }
    

    public function updateOrganizer(Organizer $organizer, OrganizerDTO $organizerDTO): Organizer
    {
        if (empty($organizerDTO->getName())) {
            throw new \InvalidArgumentException('Organizer name cannot be empty');
        }

        if (!filter_var($organizerDTO->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if (!preg_match('/^\+?[0-9]{1,4}?[-. ]?(\(?[0-9]{1,4}?\)?[-. ]?)?[0-9]{1,4}[-. ]?[0-9]{1,9}$/', $organizerDTO->getPhone())) {
            throw new \InvalidArgumentException('Invalid phone number format');
        }

        if (!empty($organizerDTO->getPassword()) && strlen($organizerDTO->getPassword()) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long');
        }

        $organizer->setName($organizerDTO->getName());
        $organizer->setEmail($organizerDTO->getEmail());
        $organizer->setPhone($organizerDTO->getPhone());

        if (!empty($organizerDTO->getPassword())) {
            $hashedPassword = password_hash($organizerDTO->getPassword(), PASSWORD_DEFAULT);
            $organizer->setPassword($hashedPassword);
        }

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
