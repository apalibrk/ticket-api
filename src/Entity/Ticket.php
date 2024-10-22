<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[OA\Schema(
    schema: "Ticket",
    description: "Represents a ticket",
    type: "object",
    required: ["seatNumber", "price", "status", "event"],
    properties: [
        new OA\Property(
            property: "id",
            description: "Unique identifier of the ticket",
            type: "integer",
            example: 1
        ),
        new OA\Property(
            property: "seatNumber",
            description: "Seat number of the ticket",
            type: "string",
            example: "A1"
        ),
        new OA\Property(
            property: "price",
            description: "Price of the ticket",
            type: "number",
            format: "float",
            example: 99.99
        ),
        new OA\Property(
            property: "status",
            description: "Status of the ticket",
            type: "string",
            example: "available"
        ),
        new OA\Property(
            property: "event",
            description: "Event associated with the ticket",
            ref: "#/components/schemas/Event"
        )
    ]
)]
class Ticket implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    private ?string $seatNumber = null;

    #[ORM\Column(type: "float")]
    private ?float $price = null;

    #[ORM\Column(type: "string", length: 40)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: "tickets")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeatNumber(): ?string
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(string $seatNumber): static
    {
        $this->seatNumber = $seatNumber;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'seatNumber' => $this->seatNumber,
            'price' => $this->price,
            'status' => $this->status,
            'event' => $this->event ? $this->event->getId() : null,
        ];
    }
}