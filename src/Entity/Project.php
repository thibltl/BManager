<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $project_desc = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $project_createdAt = null;

    #[ORM\OneToMany(targetEntity: Tasks::class, mappedBy: 'task_project', orphanRemoval: true)]
    private Collection $tasks;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projects')]
    #[ORM\JoinTable(name: 'project_members')]
    private Collection $users;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->project_createdAt = new \DateTimeImmutable();
    }

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

    public function getProjectDesc(): ?string
    {
        return $this->project_desc;
    }

    public function setProjectDesc(string $project_desc): static
    {
        $this->project_desc = $project_desc;
        return $this;
    }

    public function getProjectCreatedAt(): ?\DateTimeImmutable
    {
        return $this->project_createdAt;
    }

    public function setProjectCreatedAt(\DateTimeImmutable $project_createdAt): static
    {
        $this->project_createdAt = $project_createdAt;
        return $this;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Tasks $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setTaskProject($this);
        }
        return $this;
    }

    public function removeTask(Tasks $task): static
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getTaskProject() === $this) {
                $task->setTaskProject(null);
            }
        }
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addProject($this);
        }
        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeProject($this);
        }
        return $this;
    }
}
