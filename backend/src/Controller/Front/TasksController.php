<?php

namespace App\Controller\Front;

use App\Entity\Tasks;
use App\Entity\Status;
use App\Entity\Project;
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
    /**
     * Liste des tâches d’un projet
     */
    #[Route('/project/{id}', name: 'front_tasks_by_project', methods: ['GET'])]
    public function byProject(Project $project, TasksRepository $tasksRepository): Response
    {
        // 🔒 Vérification d’accès
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('front/tasks/index.html.twig', [
            'project' => $project,
            'tasks' => $tasksRepository->findBy(['task_project' => $project]),
        ]);
    }

    /**
     * Création d’une tâche dans un projet
     */
    #[Route('/new/{projectId}', name: 'front_tasks_new', methods: ['GET', 'POST'])]
    public function new(
        int $projectId,
        Request $request,
        EntityManagerInterface $em,
        NotificationService $notifier,
        TaskHistoryService $history
    ): Response {
        $project = $em->getRepository(Project::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Projet introuvable.');
        }

        // 🔒 Vérification d’accès
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $task = new Tasks();
        $task->setTaskProject($project);

        $form = $this->createForm(TasksType::class, $task, [
            'project_users' => $project->getUsers(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 🔒 Revalidation des utilisateurs assignés
            foreach ($task->getUsers() as $user) {
                if (!$project->getUsers()->contains($user)) {
                    throw $this->createAccessDeniedException('Utilisateur non autorisé pour ce projet.');
                }
            }

            $em->persist($task);
            $em->flush();

            $history->log($task, "Tâche créée", $this->getUser());

            foreach ($task->getUsers() as $user) {
                $notifier->notify(
                    $user,
                    "Une nouvelle tâche vous a été assignée : {$task->getTaskTitle()}",
                    "task_assigned"
                );
            }

            return $this->redirectToRoute('front_tasks_by_project', [
                'id' => $project->getId()
            ]);
        }

        return $this->render('front/tasks/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'project' => $project,
        ]);
    }

    /**
     * Affichage d’une tâche
     */
    #[Route('/{id}', name: 'front_tasks_show', methods: ['GET'])]
    public function show(Tasks $task): Response
    {
        // 🔒 Vérification d’accès
        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('front/tasks/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * Modification d’une tâche
     */
    #[Route('/{id}/edit', name: 'front_tasks_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Tasks $task,
        EntityManagerInterface $em,
        NotificationService $notifier,
        TaskHistoryService $history
    ): Response {

        // 🔒 Vérification d’accès
        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $oldProject  = $task->getTaskProject();
        $oldStatus   = $task->getTaskStatus();
        $oldUsers    = clone $task->getUsers();
        $oldTitle    = $task->getTaskTitle();
        $oldDueDate  = $task->getTaskDueDate();
        $oldPriority = $task->getTaskPriority();

        $form = $this->createForm(TasksType::class, $task, [
            'project_users' => $oldProject->getUsers(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 🔒 Empêcher le changement de projet non autorisé
            if ($task->getTaskProject() !== $oldProject) {
                // soit on bloque totalement :
                $task->setTaskProject($oldProject);
                // soit on pourrait vérifier l’accès au nouveau projet, mais ici on interdit.
            }

            // 🔒 Revalidation des utilisateurs assignés
            foreach ($task->getUsers() as $user) {
                if (!$oldProject->getUsers()->contains($user)) {
                    throw $this->createAccessDeniedException('Utilisateur non autorisé pour ce projet.');
                }
            }

            // Historique (statut, date, titre, priorité, assignations)
            if ($task->getTaskStatus() !== $oldStatus) {
                $history->log(
                    $task,
                    sprintf(
                        "Statut modifié : %s → %s",
                        $oldStatus?->getStatusName() ?? 'Aucun',
                        $task->getTaskStatus()?->getStatusName() ?? 'Aucun'
                    ),
                    $this->getUser()
                );
            }

            if ($task->getTaskDueDate() != $oldDueDate) {
                $history->log(
                    $task,
                    sprintf(
                        "Date modifiée : %s → %s",
                        $oldDueDate?->format('d/m/Y') ?? 'Aucune',
                        $task->getTaskDueDate()?->format('d/m/Y') ?? 'Aucune'
                    ),
                    $this->getUser()
                );
            }

            if ($task->getTaskTitle() !== $oldTitle) {
                $history->log(
                    $task,
                    "Titre modifié : \"{$oldTitle}\" → \"{$task->getTaskTitle()}\"",
                    $this->getUser()
                );
            }

            if ($task->getTaskPriority() !== $oldPriority) {
                $history->log(
                    $task,
                    sprintf(
                        "Priorité modifiée : %s → %s",
                        $oldPriority?->getPriorityName() ?? 'Aucune',
                        $task->getTaskPriority()?->getPriorityName() ?? 'Aucune'
                    ),
                    $this->getUser()
                );
            }

            foreach ($task->getUsers() as $user) {
                if (!$oldUsers->contains($user)) {
                    $history->log(
                        $task,
                        "Nouvel utilisateur assigné : {$user->getName()}",
                        $this->getUser()
                    );

                    $notifier->notify(
                        $user,
                        "Vous avez été assigné à la tâche : {$task->getTaskTitle()}",
                        "task_assigned"
                    );
                }
            }

            $em->flush();

            return $this->redirectToRoute('front_tasks_show', ['id' => $task->getId()]);
        }

        return $this->render('front/tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d’une tâche
     */
    #[Route('/{id}', name: 'front_tasks_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Tasks $task,
        EntityManagerInterface $em,
        TaskHistoryService $history
    ): Response {

        // 🔒 Vérification d’accès
        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $project = $task->getTaskProject();

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {

            $history->log($task, "Tâche supprimée", $this->getUser());

            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('front_tasks_by_project', [
            'id' => $project->getId()
        ]);
    }

    /**
     * Mise à jour du statut (Kanban)
     */
    #[Route('/{id}/status', name: 'front_tasks_update_status', methods: ['POST'])]
    public function updateStatus(
        int $id,
        Request $request,
        TasksRepository $tasksRepository,
        EntityManagerInterface $em,
        TaskHistoryService $history
    ): Response {
        $task = $tasksRepository->find($id);

        if (!$task) {
            return $this->json(['error' => 'Tâche non trouvée'], 404);
        }

        // 🔒 Vérification d’accès
        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        $data = json_decode($request->getContent(), true);

        // 🔒 CSRF
        if (!isset($data['_token']) || !$this->isCsrfTokenValid('task_status', $data['_token'])) {
            return $this->json(['error' => 'Token CSRF invalide'], 403);
        }

        $newStatusName = $data['status'] ?? null;

        if (!$newStatusName) {
            return $this->json(['error' => 'Statut manquant'], 400);
        }

        $status = $em->getRepository(Status::class)
            ->findOneBy(['status_name' => $newStatusName]);

        if (!$status) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $oldStatus = $task->getTaskStatus();
        $task->setTaskStatus($status);

        $em->flush();

        $history->log(
            $task,
            sprintf(
                "Statut modifié : %s → %s",
                $oldStatus?->getStatusName() ?? 'Aucun',
                $status->getStatusName()
            ),
            $this->getUser()
        );

        return $this->json([
            'success' => true,
            'taskId' => $task->getId(),
            'newStatus' => $status->getStatusName(),
        ]);
    }
}
