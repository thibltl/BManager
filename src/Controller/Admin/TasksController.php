<?php

namespace App\Controller\Admin;

use App\Entity\Tasks;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tasks')]
final class TasksController extends AbstractController
{
    #[Route(name: 'admin_tasks_index', methods: ['GET'])]
    public function index(TasksRepository $tasksRepository): Response
    {
        return $this->render('admin/tasks/index.html.twig', [
            'tasks' => $tasksRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_tasks_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Tasks();
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('admin_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tasks/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_tasks_show', methods: ['GET'])]
    public function show(Tasks $task): Response
    {
        return $this->render('admin/tasks/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_tasks_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tasks $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_tasks_delete', methods: ['POST'])]
    public function delete(Request $request, Tasks $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_tasks_index', [], Response::HTTP_SEE_OTHER);
    }
}
