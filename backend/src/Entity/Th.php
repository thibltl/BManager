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
    private ?string $th_changeLog = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $th_updateAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThChangeLog(): ?string
    {
        return $this->th_changeLog;
    }

    public function setThChangeLog(string $th_changeLog): static
    {
        $this->th_changeLog = $th_changeLog;

        return $this;
    }

    public function getThUpdateAt(): ?\DateTime
    {
        return $this->th_updateAt;
    }

    public function setThUpdateAt(\DateTime $th_updateAt): static
    {
        $this->th_updateAt = $th_updateAt;

        return $this;
    }
}
