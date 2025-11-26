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
    private ?string $task_desc = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $task_dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $task_createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $task_lastChange = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Priority $task_priority = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Status $task_status = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Project $task_project = null;

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

    public function getTaskDesc(): ?string
    {
        return $this->task_desc;
    }

    public function setTaskDesc(string $task_desc): static
    {
        $this->task_desc = $task_desc;

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

    public function getTaskCreatedAt(): ?\DateTime
    {
        return $this->task_createdAt;
    }

    public function setTaskCreatedAt(\DateTime $task_createdAt): static
    {
        $this->task_createdAt = $task_createdAt;

        return $this;
    }

    public function getTaskLastChange(): ?\DateTime
    {
        return $this->task_lastChange;
    }

    public function setTaskLastChange(\DateTime $task_lastChange): static
    {
        $this->task_lastChange = $task_lastChange;

        return $this;
    }

    public function getTaskPriority(): ?Priority
    {
        return $this->task_priority;
    }

    public function setTaskPriority(?Priority $task_priority): static
    {
        $this->task_priority = $task_priority;

        return $this;
    }

    public function getTaskStatus(): ?Status
    {
        return $this->task_status;
    }

    public function setTaskStatus(?Status $task_status): static
    {
        $this->task_status = $task_status;

        return $this;
    }

    public function getTaskProject(): ?Project
    {
        return $this->task_project;
    }

    public function setTaskProject(?Project $task_project): static
    {
        $this->task_project = $task_project;

        return $this;
    }
}
