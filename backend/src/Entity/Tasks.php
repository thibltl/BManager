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

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Status $task_status = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Priority $task_priority = null;

    #[ORM\ManyToOne(inversedBy: 'project_tasks')]
    private ?Project $project = null;

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

    public function getTaskStatus(): ?Status
    {
        return $this->task_status;
    }

    public function setTaskStatus(?Status $task_status): static
    {
        $this->task_status = $task_status;

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
