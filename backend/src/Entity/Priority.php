<?php

namespace App\Entity;

use App\Repository\PriorityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriorityRepository::class)]
class Priority
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $priority_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriorityName(): ?string
    {
        return $this->priority_name;
    }

    public function setPriorityName(string $priority_name): static
    {
        $this->priority_name = $priority_name;

        return $this;
    }
}
