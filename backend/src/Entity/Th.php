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
    private ?string $th_change_log = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $th_updateat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThChangeLog(): ?string
    {
        return $this->th_change_log;
    }

    public function setThChangeLog(string $th_change_log): static
    {
        $this->th_change_log = $th_change_log;

        return $this;
    }

    public function getThUpdateat(): ?\DateTime
    {
        return $this->th_updateat;
    }

    public function setThUpdateat(\DateTime $th_updateat): static
    {
        $this->th_updateat = $th_updateat;

        return $this;
    }
}
