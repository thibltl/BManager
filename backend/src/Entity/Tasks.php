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
    private ?string $tasks_title = null;

    #[ORM\Column(length: 255)]
    private ?string $tasks_desciption = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $tasks_duedate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $tasks_createdat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $tasks_laschange = null;

    #[ORM\ManyToOne(inversedBy: 'theTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projects $projects_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTasksTitle(): ?string
    {
        return $this->tasks_title;
    }

    public function setTasksTitle(string $tasks_title): static
    {
        $this->tasks_title = $tasks_title;

        return $this;
    }

    public function getTasksDesciption(): ?string
    {
        return $this->tasks_desciption;
    }

    public function setTasksDesciption(string $tasks_desciption): static
    {
        $this->tasks_desciption = $tasks_desciption;

        return $this;
    }

    public function getTasksDuedate(): ?\DateTime
    {
        return $this->tasks_duedate;
    }

    public function setTasksDuedate(\DateTime $tasks_duedate): static
    {
        $this->tasks_duedate = $tasks_duedate;

        return $this;
    }

    public function getTasksCreatedat(): ?\DateTime
    {
        return $this->tasks_createdat;
    }

    public function setTasksCreatedat(\DateTime $tasks_createdat): static
    {
        $this->tasks_createdat = $tasks_createdat;

        return $this;
    }

    public function getTasksLaschange(): ?\DateTime
    {
        return $this->tasks_laschange;
    }

    public function setTasksLaschange(\DateTime $tasks_laschange): static
    {
        $this->tasks_laschange = $tasks_laschange;

        return $this;
    }

    public function getProjectsId(): ?Projects
    {
        return $this->projects_id;
    }

    public function setProjectsId(?Projects $projects_id): static
    {
        $this->projects_id = $projects_id;

        return $this;
    }
}
