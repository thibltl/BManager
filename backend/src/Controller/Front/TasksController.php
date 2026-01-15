<?php

namespace App\Controller\Front;

use App\Entity\Tasks;
use App\Entity\Status;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use App\Service\NotificationService;
use App\Service\TaskHistoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/tasks')]
final class TasksController extends AbstractController
{
    #[Route(name: 'front_tasks_index', methods: ['GET'])]
    public function index(TasksRepository $tasksRepository): Response
    {
        return $this->render('front/tasks/index.html.twig', [
            'tasks' => $tasksRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'front_tasks_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        NotificationService $notifier,
        TaskHistoryService $history
    ): Response {
        $task = new Tasks();
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setTaskCreatedAt(new \DateTime());
            $task->setTaskLastChange(new \DateTime());

            $entityManager->persist($task);
            $entityManager->flush();

            // Historique création
            $history->log($task, "Tâche créée", $this->getUser());

            // Notifications aux utilisateurs assignés
            foreach ($task->getUsers() as $user) {
                $notifier->notify(
                    $user,
                    "Une nouvelle tâche vous a été assignée : {$task->getTaskTitle()}"
                );
            }

            return $this->redirectToRoute('front_tasks_index');
        }

        return $this->render('front/tasks/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'front_tasks_show', methods: ['GET'])]
    public function show(Tasks $task): Response
    {
        return $this->render('front/tasks/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'front_tasks_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Tasks $task,
        EntityManagerInterface $entityManager,
        NotificationService $notifier,
        TaskHistoryService $history
    ): Response {

        // Sauvegarde des anciennes valeurs
        $oldStatus = $task->getTaskStatus();
        $oldUsers = clone $task->getUsers();
        $oldTitle = $task->getTaskTitle();
        $oldDueDate = $task->getTaskDueDate();

        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Changement de statut
            if ($task->getTaskStatus() !== $oldStatus) {
                $history->log(
                    $task,
                    "Statut modifié : {$oldStatus->getStatusName()} → {$task->getTaskStatus()->getStatusName()}",
                    $this->getUser()
                );
            }

            // Changement de date
            if ($task->getTaskDueDate() != $oldDueDate) {
                $history->log(
                    $task,
                    "Date modifiée : {$oldDueDate?->format('d/m/Y')} → {$task->getTaskDueDate()?->format('d/m/Y')}",
                    $this->getUser()
                );
            }

            // Changement de titre
            if ($task->getTaskTitle() !== $oldTitle) {
                $history->log(
                    $task,
                    "Titre modifié : \"$oldTitle\" → \"{$task->getTaskTitle()}\"",
                    $this->getUser()
                );
            }

            // Nouveaux utilisateurs assignés
            foreach ($task->getUsers() as $user) {
                if (!$oldUsers->contains($user)) {

                    $history->log(
                        $task,
                        "Nouvel utilisateur assigné : {$user->getName()}",
                        $this->getUser()
                    );

                    $notifier->notify(
                        $user,
                        "Vous avez été assigné à la tâche : {$task->getTaskTitle()}"
                    );
                }
            }

            $task->setTaskLastChange(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('front_tasks_show', ['id' => $task->getId()]);
        }

        return $this->render('front/tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'front_tasks_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Tasks $task,
        EntityManagerInterface $entityManager,
        TaskHistoryService $history
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {

            $history->log($task, "Tâche supprimée", $this->getUser());

            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_tasks_index');
    }

    #[Route('/{id}/status', name: 'front_tasks_update_status', methods: ['POST'])]
    public function updateStatus(
        int $id,
        Request $request,
        TasksRepository $tasksRepository,
        EntityManagerInterface $entityManager,
        TaskHistoryService $history
    ): Response {
        $task = $tasksRepository->find($id);
        if (!$task) {
            return $this->json(['error' => 'Tâche non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $newStatusName = $data['status'] ?? null;

        if (!$newStatusName) {
            return $this->json(['error' => 'Statut manquant'], 400);
        }

        $status = $entityManager->getRepository(Status::class)
            ->findOneBy(['status_name' => $newStatusName]);

        if (!$status) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $oldStatus = $task->getTaskStatus();
        $task->setTaskStatus($status);
        $task->setTaskLastChange(new \DateTime());

        $entityManager->flush();

        // Historique
        $history->log(
            $task,
            "Statut modifié : {$oldStatus->getStatusName()} → {$status->getStatusName()}",
            $this->getUser()
        );

        return $this->json([
            'success' => true,
            'taskId' => $task->getId(),
            'newStatus' => $status->getStatusName(),
        ]);
    }
}
