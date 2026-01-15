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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $th_updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'history')]
    private ?Tasks $task = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $author = null;

    public function __construct()
    {
        $this->th_updatedAt = new \DateTime();
    }

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

    public function getThUpdatedAt(): ?\DateTime
    {
        return $this->th_updatedAt;
    }

    public function setThUpdatedAt(\DateTime $th_updatedAt): static
    {
        $this->th_updatedAt = $th_updatedAt;
        return $this;
    }

    public function getTask(): ?Tasks
    {
        return $this->task;
    }

    public function setTask(?Tasks $task): static
    {
        $this->task = $task;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }
}
