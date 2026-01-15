<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private HubInterface $hub
    ) {}

    /**
     * Envoie une notification interne à un utilisateur
     * + publication Mercure en temps réel
     */
    public function notify(User $user, string $message, string $type): void
    {
        // Vérification des préférences utilisateur
        $prefs = $user->getNotificationPreferences();
        if (isset($prefs[$type]) && $prefs[$type] === false) {
            return; // Notification désactivée pour ce type
        }

        // Création de la notification
        $notif = new Notification();
        $notif->setUser($user);
        $notif->setMessage($message);
        $notif->setIsRead(false);
        $notif->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($notif);
        $this->em->flush();

        // Publication Mercure (temps réel)
        $update = new Update(
            topic: "user/{$user->getId()}/notifications",
            data: json_encode([
                'id'        => $notif->getId(),
                'message'   => $notif->getMessage(),
                'createdAt' => $notif->getCreatedAt()->format('Y-m-d H:i:s'),
                'type'      => $type,
            ])
        );

        $this->hub->publish($update);
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead(Notification $notif): void
    {
        $notif->setIsRead(true);
        $this->em->flush();
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsRead(User $user): void
    {
        foreach ($user->getNotifications() as $notif) {
            $notif->setIsRead(true);
        }
        $this->em->flush();
    }
}
