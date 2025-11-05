<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $project_name = null;

    #[ORM\Column(length: 255)]
    private ?string $project_description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $project_createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->project_name;
    }

    public function setProjectName(string $project_name): static
    {
        $this->project_name = $project_name;

        return $this;
    }

    public function getProjectDescription(): ?string
    {
        return $this->project_description;
    }

    public function setProjectDescription(string $project_description): static
    {
        $this->project_description = $project_description;

        return $this;
    }

    public function getProjectCreatedAt(): ?\DateTime
    {
        return $this->project_createdAt;
    }

    public function setProjectCreatedAt(\DateTime $project_createdAt): static
    {
        $this->project_createdAt = $project_createdAt;

        return $this;
    }
}
