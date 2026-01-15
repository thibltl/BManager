<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function index(EntityManagerInterface $em)
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('front/notifications/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/notifications/read/{id}', name: 'notifications_read')]
    public function read(Notification $notification, EntityManagerInterface $em)
    {
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);
        $em->flush();

        return $this->redirectToRoute('notifications');
    }

    #[Route('/notifications/read-all', name: 'notifications_read_all')]
    public function readAll(EntityManagerInterface $em)
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy(['user' => $this->getUser(), 'isRead' => false]);

        foreach ($notifications as $notif) {
            $notif->setIsRead(true);
        }

        $em->flush();

        return $this->redirectToRoute('notifications');
    }
}
