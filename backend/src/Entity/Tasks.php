<?php

namespace App\Entity;

use App\Repository\TasksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TasksRepository::class)]
class Tasks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $task_title = null;

    #[ORM\Column(length: 255)]
    private ?string $task_description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $task_dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $task_createAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $task_lastchange = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskTitle(): ?string
    {
        return $this->task_title;
    }

    public function setTaskTitle(string $task_title): static
    {
        $this->task_title = $task_title;

        return $this;
    }

    public function getTaskDescription(): ?string
    {
        return $this->task_description;
    }

    public function setTaskDescription(string $task_description): static
    {
        $this->task_description = $task_description;

        return $this;
    }

    public function getTaskDueDate(): ?\DateTime
    {
        return $this->task_dueDate;
    }

    public function setTaskDueDate(\DateTime $task_dueDate): static
    {
        $this->task_dueDate = $task_dueDate;

        return $this;
    }

    public function getTaskCreateAt(): ?\DateTime
    {
        return $this->task_createAt;
    }

    public function setTaskCreateAt(\DateTime $task_createAt): static
    {
        $this->task_createAt = $task_createAt;

        return $this;
    }

    public function getTaskLastchange(): ?\DateTime
    {
        return $this->task_lastchange;
    }

    public function setTaskLastchange(\DateTime $task_lastchange): static
    {
        $this->task_lastchange = $task_lastchange;

        return $this;
    }
}
