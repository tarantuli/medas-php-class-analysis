<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Column(type: Types::STRING, length: SIZE)]
    public string $name;

    #[Assert\NotBlank, Assert\Length(max: 255)]
    public string $email;

    public function rename(#[SensitiveParameter] string $name): void
    {
    }
}
