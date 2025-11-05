<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $users_id = null;

    #[ORM\Column(length: 255)]
    private ?string $users_name = null;

    #[ORM\Column(length: 255)]
    private ?string $users_email = null;

    #[ORM\Column(length: 255)]
    private ?string $users_password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $users_createdat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsersId(): ?int
    {
        return $this->users_id;
    }

    public function setUsersId(int $users_id): static
    {
        $this->users_id = $users_id;

        return $this;
    }

    public function getUsersName(): ?string
    {
        return $this->users_name;
    }

    public function setUsersName(string $users_name): static
    {
        $this->users_name = $users_name;

        return $this;
    }

    public function getUsersEmail(): ?string
    {
        return $this->users_email;
    }

    public function setUsersEmail(string $users_email): static
    {
        $this->users_email = $users_email;

        return $this;
    }

    public function getUsersPassword(): ?string
    {
        return $this->users_password;
    }

    public function setUsersPassword(string $users_password): static
    {
        $this->users_password = $users_password;

        return $this;
    }

    public function getUsersCreatedat(): ?\DateTime
    {
        return $this->users_createdat;
    }

    public function setUsersCreatedat(\DateTime $users_createdat): static
    {
        $this->users_createdat = $users_createdat;

        return $this;
    }
}
