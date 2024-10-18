<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[OA\Schema(description: "Represents an event")]
class Event implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: "Unique identifier of the event", example: 1)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: "Title of the event", example: "SymfonyCon 2024")]
    private ?string $title = null;

    #[ORM\Column(type: "datetime")]
    #[OA\Property(type: "string", format: "date-time", description: "Date and time of the event", example: "2024-12-12T19:30:00Z")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: "Venue of the event", example: "Berlin, Germany")]
    private ?string $venue = null;

    #[ORM\Column]
    #[OA\Property(description: "Capacity of the event", example: 500)]
    private ?int $capacity = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "Organizer of the event", ref: '#/components/schemas/Organizer')]
    private ?Organizer $organizer = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Ticket::class, orphanRemoval: true)]
    #[OA\Property(description: "Tickets associated with the event", type: "array", items: new OA\Items(ref: '#/components/schemas/Ticket'))]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): static
    {
        $this->venue = $venue;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }

    public function setOrganizer(?Organizer $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setEvent($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getEvent() === $this) {
                $ticket->setEvent(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'venue' => $this->venue,
            'capacity' => $this->capacity,
            'organizer' => $this->organizer ? $this->organizer->getId() : null,
            'tickets' => $this->tickets->map(fn($ticket) => $ticket->getId())->toArray(),
        ];
    }
}