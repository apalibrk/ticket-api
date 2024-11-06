<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TicketDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    private string $seatNumber;

    #[Assert\NotBlank]
    #[Assert\Positive]
    private float $price;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['available', 'sold', 'reserved'], message: 'Choose a valid status.')]
    private string $status;

    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $eventId;

    public function getSeatNumber(): string
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(string $seatNumber): self
    {
        $this->seatNumber = $seatNumber;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId): self
    {
        $this->eventId = $eventId;
        return $this;
    }
}