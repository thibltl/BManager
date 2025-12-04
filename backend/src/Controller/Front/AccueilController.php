<?php

namespace App\Controller\Front;

use App\Repository\ProjectRepository;
use App\Repository\TasksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(ProjectRepository $projectRepository, TasksRepository $tasksRepository): Response
    {
        $projects = $projectRepository->findAll();
        $tasks = $tasksRepository->findAll();

        return $this->render('front/accueil/index.html.twig', [
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
}
