<?php

namespace App\Entity;

use App\Repository\OrganizerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: OrganizerRepository::class)]
#[OA\Schema(description: "Represents an organizer")]
class Organizer implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: "Unique identifier of the organizer", example: 1)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: "Name of the organizer", example: "John Doe")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: "Email of the organizer", example: "john.doe@example.com")]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[OA\Property(description: "Phone number of the organizer", example: "+1234567890")]
    private ?string $phone = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'organizer')]
    #[OA\Property(description: "Events organized by the organizer", type: "array", items: new OA\Items(ref: '#/components/schemas/Event'))]
    private Collection $events;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: "Password of the organizer", example: "password")]
    private ?string $password = null;

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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
    
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            // Avoid serializing the password for security reasons
        ];
    }
}