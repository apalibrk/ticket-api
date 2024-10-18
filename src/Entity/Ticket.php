<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[OA\Schema(description: "Represents a ticket")]
class Ticket implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: "Unique identifier of the ticket", example: 1)]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[OA\Property(description: "Seat number of the ticket", example: "A1")]
    private ?string $seatNumber = null;

    #[ORM\Column]
    #[OA\Property(description: "Price of the ticket", example: 99.99)]
    private ?float $price = null;

    #[ORM\Column(length: 40)]
    #[OA\Property(description: "Status of the ticket", example: "available")]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[OA\Property(description: "Event associated with the ticket", ref: '#/components/schemas/Event')]
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

    public function jsonSerialize()
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