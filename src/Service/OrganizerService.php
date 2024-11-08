<?php

namespace App\Service;

use App\Entity\Organizer;
use App\DTO\OrganizerDTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizerRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Service class for managing organizers.
 */
class OrganizerService
{
    private EntityManagerInterface $entityManager;
    private OrganizerRepository $organizerRepository;

    public function __construct(EntityManagerInterface $entityManager, OrganizerRepository $organizerRepository)
    {
        $this->entityManager = $entityManager;
        $this->organizerRepository = $organizerRepository;
    }

    /**
     * Creates a new organizer.
     *
     * @param OrganizerDTO $organizerDTO The data transfer object containing organizer details.
     * @return Organizer The created organizer.
     * @throws \InvalidArgumentException If the organizer data is invalid.
     */
    public function createOrganizer(OrganizerDTO $organizerDTO): Organizer
    {
        $organizer = new Organizer();
        $organizer->setName($organizerDTO->getName());
        $organizer->setEmail($organizerDTO->getEmail());
        $organizer->setPhone($organizerDTO->getPhone());
        $organizer->setPassword($organizerDTO->getPassword());
    
        $this->entityManager->persist($organizer);
        $this->entityManager->flush();
    
        return $organizer;
    }

    /**
     * Updates an existing organizer.
     *
     * @param Organizer $organizer The organizer to update.
     * @param OrganizerDTO $organizerDTO The data transfer object containing updated organizer details.
     * @return Organizer The updated organizer.
     * @throws \InvalidArgumentException If the organizer data is invalid.
     */
    public function updateOrganizer(Organizer $organizer, OrganizerDTO $organizerDTO): Organizer
    {
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

    /**
     * Deletes an organizer.
     *
     * @param Organizer $organizer The organizer to delete.
     */
    public function deleteOrganizer(Organizer $organizer): void
    {
        $this->entityManager->remove($organizer);
        $this->entityManager->flush();
    }

    /**
     * Retrieves all organizers.
     *
     * @return Organizer[] An array of all organizers.
     */
    public function findAll(): array
    {
        return $this->organizerRepository->findAll();
    }

    public function findOneByEmail(string $email): ?Organizer
    {
        return $this->organizerRepository->findOneBy(['email' => $email]);
    }

}