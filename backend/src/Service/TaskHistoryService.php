<?php

namespace App\Service;

use App\Entity\Th;
use App\Entity\Tasks;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskHistoryService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function log(Tasks $task, string $message, ?User $author = null): void
    {
        $entry = new Th();
        $entry->setTask($task);
        $entry->setThChangelog($message);
        $entry->setAuthor($author);

        $this->em->persist($entry);
        $this->em->flush();
    }
}
