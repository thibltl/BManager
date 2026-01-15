<?php

namespace App\Entity;

use App\Repository\TasksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $task_desc = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $task_dueDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $task_createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $task_lastChange = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Priority $task_priority = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Status $task_status = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Project $task_project = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tasks')]
    private Collection $users;

    /**
     * @var Collection<int, Th>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Th::class, cascade: ['persist', 'remove'])]
    private Collection $history;

    #[ORM\Column]
    private int $position = 0;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->history = new ArrayCollection();
        $this->task_createdAt = new \DateTimeImmutable();
        $this->task_lastChange = new \DateTimeImmutable();
    }

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
        $this->touch();
        return $this;
    }

    public function getTaskDesc(): ?string
    {
        return $this->task_desc;
    }

    public function setTaskDesc(string $task_desc): static
    {
        $this->task_desc = $task_desc;
        $this->touch();
        return $this;
    }

    public function getTaskDueDate(): ?\DateTimeImmutable
    {
        return $this->task_dueDate;
    }

    public function setTaskDueDate(\DateTimeImmutable $task_dueDate): static
    {
        $this->task_dueDate = $task_dueDate;
        $this->touch();
        return $this;
    }

    public function getTaskCreatedAt(): ?\DateTimeImmutable
    {
        return $this->task_createdAt;
    }

    public function setTaskCreatedAt(\DateTimeImmutable $task_createdAt): static
    {
        $this->task_createdAt = $task_createdAt;
        return $this;
    }

    public function getTaskLastChange(): ?\DateTimeImmutable
    {
        return $this->task_lastChange;
    }

    public function setTaskLastChange(\DateTimeImmutable $task_lastChange): static
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
        $this->touch();
        return $this;
    }

    public function getTaskStatus(): ?Status
    {
        return $this->task_status;
    }

    public function setTaskStatus(?Status $task_status): static
    {
        $this->task_status = $task_status;
        $this->touch();
        return $this;
    }

    public function getTaskProject(): ?Project
    {
        return $this->task_project;
    }

    public function setTaskProject(?Project $task_project): static
    {
        $this->task_project = $task_project;
        $this->touch();
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTask($this);
            $this->touch();
        }
        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeTask($this);
            $this->touch();
        }
        return $this;
    }

    /**
     * @return Collection<int, Th>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(Th $entry): static
    {
        if (!$this->history->contains($entry)) {
            $this->history->add($entry);
            $entry->setTask($this);
        }
        return $this;
    }

    public function removeHistory(Th $entry): static
    {
        if ($this->history->removeElement($entry)) {
            if ($entry->getTask() === $this) {
                $entry->setTask(null);
            }
        }
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function isLate(): bool
    {
        if (!$this->task_dueDate) {
            return false;
        }

        $now = new \DateTimeImmutable();
        $isTerminee = $this->task_status && method_exists($this->task_status, 'getName')
            ? mb_strtolower($this->task_status->getName()) === 'terminée'
            : false;

        return $this->task_dueDate < $now && !$isTerminee;
    }

    private function touch(): void
    {
        $this->task_lastChange = new \DateTimeImmutable();
    }
}
