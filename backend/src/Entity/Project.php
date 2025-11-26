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

    #[ORM\Column(length: 255)]
    private ?string $project_desc = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $project_createdAt = null;

    /**
     * @var Collection<int, Tasks>
     */
    #[ORM\OneToMany(targetEntity: Tasks::class, mappedBy: 'task_project')]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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

    public function getProjectCreatedAt(): ?\DateTime
    {
        return $this->project_createdAt;
    }

    public function setProjectCreatedAt(\DateTime $project_createdAt): static
    {
        $this->project_createdAt = $project_createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Tasks>
     */
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
            // set the owning side to null (unless already changed)
            if ($task->getTaskProject() === $this) {
                $task->setTaskProject(null);
            }
        }

        return $this;
    }
}
