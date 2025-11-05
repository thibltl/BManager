<?php

namespace App\Entity;

use App\Repository\ThRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThRepository::class)]
class Th
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $th_changelog = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $th_update = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThChangelog(): ?string
    {
        return $this->th_changelog;
    }

    public function setThChangelog(string $th_changelog): static
    {
        $this->th_changelog = $th_changelog;

        return $this;
    }

    public function getThUpdate(): ?\DateTime
    {
        return $this->th_update;
    }

    public function setThUpdate(\DateTime $th_update): static
    {
        $this->th_update = $th_update;

        return $this;
    }
}
