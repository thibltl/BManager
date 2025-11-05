<?php

namespace App\Entity;

use App\Repository\ProjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectsRepository::class)]
class Projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $projects_name = null;

    #[ORM\Column(length: 255)]
    private ?string $projects_description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $projects_createdat = null;

    /**
     * @var Collection<int, Tasks>
     */
    #[ORM\OneToMany(targetEntity: Tasks::class, mappedBy: 'projects_id')]
    private Collection $theTasks;

    public function __construct()
    {
        $this->theTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectsName(): ?string
    {
        return $this->projects_name;
    }

    public function setProjectsName(string $projects_name): static
    {
        $this->projects_name = $projects_name;

        return $this;
    }

    public function getProjectsDescription(): ?string
    {
        return $this->projects_description;
    }

    public function setProjectsDescription(string $projects_description): static
    {
        $this->projects_description = $projects_description;

        return $this;
    }

    public function getProjectsCreatedat(): ?\DateTime
    {
        return $this->projects_createdat;
    }

    public function setProjectsCreatedat(\DateTime $projects_createdat): static
    {
        $this->projects_createdat = $projects_createdat;

        return $this;
    }

    /**
     * @return Collection<int, Tasks>
     */
    public function getTheTasks(): Collection
    {
        return $this->theTasks;
    }

    public function addTheTask(Tasks $theTask): static
    {
        if (!$this->theTasks->contains($theTask)) {
            $this->theTasks->add($theTask);
            $theTask->setProjectsId($this);
        }

        return $this;
    }

    public function removeTheTask(Tasks $theTask): static
    {
        if ($this->theTasks->removeElement($theTask)) {
            // set the owning side to null (unless already changed)
            if ($theTask->getProjectsId() === $this) {
                $theTask->setProjectsId(null);
            }
        }

        return $this;
    }
}
