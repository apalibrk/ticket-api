<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrganizerDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $phone;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    private string $password;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
}