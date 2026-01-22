<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks; 
use App\Entity\Project;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function notify(
        User $user,
        string $type,
        string $message,
        ?string $link = null
    ): void {
        // 1. Création de la notification
        $notification = new Notification();
        
        // CORRECTION : On utilise setUsers car ta propriété s'appelle $users
        $notification->setUsers($user); 
        
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setLink($link);
        
        // Si tu as la méthode setIsRead
        if (method_exists($notification, 'setIsRead')) {
            $notification->setIsRead(false);
        }
        $notification->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($notification);
        $this->em->flush();
    }

    public function notifyTaskAssigned(User $receiver, Tasks $task, Project $project, User $assigner): void
    {
        $message = sprintf(
            "%s vous a assigné la tâche “%s” dans le projet “%s”.",
            $assigner->getName(),
            $task->getTaskTitle(),
            $project->getProjectName()
        );

        $notification = new Notification();
        $notification->setUsers($receiver);
        $notification->setType('task_assigned');
        $notification->setMessage($message);
        $notification->setLink('/front/tasks/show/' . $task->getId());
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($notification);
        $this->em->flush();
    }

    
}