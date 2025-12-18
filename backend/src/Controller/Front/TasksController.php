<?php

namespace App\Controller\Front;

use App\Entity\Tasks;
use App\Entity\Status;
use App\Form\TasksType;
use App\Repository\TasksRepository;
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Tasks();
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('front_tasks_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(Request $request, Tasks $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'front_tasks_delete', methods: ['POST'])]
    public function delete(Request $request, Tasks $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_tasks_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/status', name: 'front_tasks_update_status', methods: ['POST'])]
    public function updateStatus(
        int $id,
        Request $request,
        TasksRepository $tasksRepository,
        EntityManagerInterface $entityManager
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

        // ⚠️ Ici on utilise bien 'status_name' qui correspond à ta colonne
        $status = $entityManager->getRepository(Status::class)
            ->findOneBy(['status_name' => $newStatusName]);

        if (!$status) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $task->setTaskStatus($status);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'taskId' => $task->getId(),
            'newStatus' => $status->getStatusName(),
        ]);
    }

}
