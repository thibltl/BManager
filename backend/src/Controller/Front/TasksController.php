<?php

namespace App\Controller\Front;

use App\Entity\Tasks;
use App\Entity\Status;
use App\Entity\Priority;
use App\Entity\Project;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use App\Service\TaskHistoryService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/tasks')]
final class TasksController extends AbstractController
{
<<<<<<< HEAD
    /**
     * Liste globale des tâches de l'utilisateur
     */
    #[Route('/', name: 'front_tasks_index', methods: ['GET'])]
    public function index(TasksRepository $tasksRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $tasks = $tasksRepository->findForUser($this->getUser());

        return $this->render('front/tasks/index.html.twig', [
            'tasks' => $tasks,
            'project' => null,
        ]);
    }

    /**
     * Liste des tâches d’un projet
     */
=======
>>>>>>> d1cec7c (notification)
    #[Route('/project/{id}', name: 'front_tasks_by_project', methods: ['GET'])]
    public function byProject(Project $project, TasksRepository $tasksRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('front/tasks/index.html.twig', [
            'project' => $project,
            'tasks' => $tasksRepository->findBy(['task_project' => $project]),
        ]);
    }

<<<<<<< HEAD
    /**
     * Création d’une tâche dans un projet
     */
=======
    #[Route('/front/tasks', name: 'front_tasks_index')]
    public function index(TasksRepository $tasksRepository): Response
    {
        return $this->render('front/tasks/index.html.twig', [
            'tasks' => $tasksRepository->findAll(),
        ]);
    }

>>>>>>> d1cec7c (notification)
    #[Route('/new/{projectId}', name: 'front_tasks_new', methods: ['GET', 'POST'])]
    public function new(
        int $projectId,
        Request $request,
        EntityManagerInterface $em,
        TaskHistoryService $history,
        NotificationService $notificationService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $project = $em->getRepository(Project::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Projet introuvable.');
        }
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $task = new Tasks();
        $task->setTaskProject($project);

<<<<<<< HEAD
        $assignableUsers = $project->getUsers()->filter(fn($u) =>
            in_array('ROLE_USER', $u->getRoles())
        )->toArray();

        $form = $this->createForm(TasksType::class, $task, [
            'project_users' => $assignableUsers,
        ]);
=======
        $assignableUsers = $project->getUsers()->filter(fn($u) => in_array('ROLE_USER', $u->getRoles()))->toArray();
>>>>>>> d1cec7c (notification)

        $form = $this->createForm(TasksType::class, $task, ['project_users' => $assignableUsers]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($task->getUsers() as $user) {
                if (!$project->getUsers()->contains($user)) {
                    throw $this->createAccessDeniedException('Utilisateur non autorisé.');
                }
            }

            if (!$task->getTaskStatus()) {
                $defaultStatus = $em->getRepository(Status::class)->findOneBy(['status_name' => 'À faire']);
                if ($defaultStatus) {
                    $task->setTaskStatus($defaultStatus);
                }
            }

            if (!$task->getTaskPriority()) {
                $defaultPriority = $em->getRepository(Priority::class)->findOneBy(['priority_name' => 'Moyenne']);
                if ($defaultPriority) {
                    $task->setTaskPriority($defaultPriority);
                }
            }

            $em->persist($task);
            $em->flush();

            $history->log($task, "Tâche créée", $this->getUser());

            // --- NOTIFICATIONS ---

            // 1. Pour les utilisateurs assignés (sauf le créateur)
            foreach ($task->getUsers() as $user) {
                if ($user !== $this->getUser()) {
                    $notificationService->notify(
                        $user,
                        'task_assigned',
                        sprintf(
                            "%s vous a assigné la tâche “%s” dans le projet “%s”.",
                            $this->getUser()->getName(),
                            $task->getTaskTitle(),
                            $project->getProjectName()
                        ),
                        $this->generateUrl('front_tasks_show', ['id' => $task->getId()])
                    );
                }
            }

            // 2. Pour le créateur
            $notificationService->notify(
                $this->getUser(),
                'task_created',
                sprintf(
                    "Vous avez créé la tâche “%s” dans le projet “%s”.",
                    $task->getTaskTitle(),
                    $project->getProjectName()
                ),
                $this->generateUrl('front_tasks_show', ['id' => $task->getId()])
            );

            // ---------------------

            return $this->redirectToRoute('front_tasks_by_project', ['id' => $project->getId()]);
        }

        return $this->render('front/tasks/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'project' => $project,
        ]);
    }

    #[Route('/show/{id}', name: 'front_tasks_show', methods: ['GET'])]
    public function show(Tasks $task): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('front/tasks/show.html.twig', ['task' => $task]);
    }

    #[Route('/{id}/edit', name: 'front_tasks_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Tasks $task,
        EntityManagerInterface $em,
        TaskHistoryService $history,
        NotificationService $notificationService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $oldUsers = clone $task->getUsers();

<<<<<<< HEAD
        $assignableUsers = $oldProject->getUsers()->filter(fn($u) =>
            in_array('ROLE_USER', $u->getRoles())
        )->toArray();

        $form = $this->createForm(TasksType::class, $task, [
            'project_users' => $assignableUsers,
        ]);
=======
        $assignableUsers = $task->getTaskProject()->getUsers()->filter(fn($u) => in_array('ROLE_USER', $u->getRoles()))->toArray();
>>>>>>> d1cec7c (notification)

        $form = $this->createForm(TasksType::class, $task, ['project_users' => $assignableUsers]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $history->log($task, "Tâche modifiée", $this->getUser());

            // --- NOTIFICATIONS ---

            // 1. Nouveaux utilisateurs assignés
            foreach ($task->getUsers() as $user) {
                if (!$oldUsers->contains($user) && $user !== $this->getUser()) {
                    $notificationService->notify(
                        $user,
                        'task_assigned',
                        sprintf(
                            "%s vous a ajouté à la tâche “%s” dans le projet “%s”.",
                            $this->getUser()->getName(),
                            $task->getTaskTitle(),
                            $task->getTaskProject()->getProjectName()
                        ),
                        $this->generateUrl('front_tasks_show', ['id' => $task->getId()])
                    );
                }
            }

            // 2. Notification pour le créateur
            $notificationService->notify(
                $this->getUser(),
                'task_updated',
                sprintf(
                    "Vous avez modifié la tâche “%s” dans le projet “%s”.",
                    $task->getTaskTitle(),
                    $task->getTaskProject()->getProjectName()
                ),
                $this->generateUrl('front_tasks_show', ['id' => $task->getId()])
            );

            // ---------------------

            return $this->redirectToRoute('front_tasks_show', ['id' => $task->getId()]);
        }

        return $this->render('front/tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'front_tasks_delete', methods: ['POST'])]
<<<<<<< HEAD
    public function delete(
        Request $request,
        Tasks $task,
        EntityManagerInterface $em,
        TaskHistoryService $history
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

=======
    public function delete(Request $request, Tasks $task, EntityManagerInterface $em, TaskHistoryService $history): Response
    {
>>>>>>> d1cec7c (notification)
        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $project = $task->getTaskProject();

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $history->log($task, "Tâche supprimée", $this->getUser());
            $em->remove($task);
            $em->flush();
        }

<<<<<<< HEAD
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
        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = $tasksRepository->find($id);

        if (!$task) {
            return $this->json(['error' => 'Tâche non trouvée'], 404);
        }

        if (!$task->getTaskProject()->getUsers()->contains($this->getUser())) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        $data = json_decode($request->getContent(), true);

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
=======
        return $this->redirectToRoute('front_tasks_by_project', ['id' => $project->getId()]);
>>>>>>> d1cec7c (notification)
    }
}
