<?php

namespace App\DTO;

class EventDTO
{
    public ?int $id;
    public string $title;
    public \DateTimeInterface $date;
    public string $venue;
    public int $capacity;
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
