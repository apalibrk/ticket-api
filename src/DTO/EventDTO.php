<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EventDTO
{
    #[Assert\PositiveOrZero]
    public ?int $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public \DateTimeInterface $date;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $venue;

    #[Assert\Positive]
    public int $capacity;

    #[Assert\PositiveOrZero]
    public ?int $organizerId;

    public function __construct(
        ?int $id,
        string $title,
        \DateTimeInterface $date,
        string $venue,
        int $capacity,
        ?int $organizerId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->date = $date;
        $this->venue = $venue;
        $this->capacity = $capacity;
        $this->organizerId = $organizerId;
    }
}