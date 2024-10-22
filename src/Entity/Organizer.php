<?php

namespace App\Entity;

use App\Repository\OrganizerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: OrganizerRepository::class)]
#[OA\Schema(
    schema: "Organizer",
    description: "Represents an organizer",
    type: "object",
    required: ["name", "email", "password"],
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "Unique identifier of the organizer",
            example: 1
        ),
        new OA\Property(
            property: "name",
            type: "string",
            description: "Name of the organizer",
            example: "John Doe"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            description: "Email of the organizer",
            example: "john.doe@example.com"
        ),
        new OA\Property(
            property: "phone",
            type: "string",
            description: "Phone number of the organizer",
            example: "+1234567890"
        ),
        new OA\Property(
            property: "password",
            type: "string",
            description: "Password of the organizer",
            example: "password"
        ),
        new OA\Property(
            property: "events",
            type: "array",
            description: "Events organized by the organizer",
            items: new OA\Items(ref: "#/components/schemas/Event")
        )
    ]
)]
class Organizer implements UserInterface, PasswordAuthenticatedUserInterface, \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(
        description: "Unique identifier of the organizer", 
        type: "integer", 
        example: 1
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(
        description: "Name of the organizer", 
        type: "string", 
        example: "John Doe"
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(
        description: "Email of the organizer", 
        type: "string", 
        example: "john.doe@example.com"
    )]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[OA\Property(
        description: "Phone number of the organizer", 
        type: "string", 
        example: "+1234567890"
    )]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(
        description: "Password of the organizer", 
        type: "string", 
        example: "password"
    )]
    private ?string $password = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'organizer')]
    #[OA\Property(
        description: "Events organized by the organizer", 
        type: "array", 
        items: new OA\Items(ref: '#/components/schemas/Event')
    )]
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOrganizer($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getOrganizer() === $this) {
                $event->setOrganizer(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $plainPassword)
    {
        $this->password = $plainPassword;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'events' => $this->events->map(fn(Event $event) => $event->getId())->toArray(),
        ];
    }

    // UserInterface methods
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}