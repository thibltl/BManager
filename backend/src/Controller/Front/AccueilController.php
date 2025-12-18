<?php

namespace App\Controller\Front;

use App\Repository\ProjectRepository;
use App\Repository\TasksRepository; // ✅ avec un "s"
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'front_accueil')]
    public function accueil(ProjectRepository $projectRepository, TasksRepository $tasksRepository, UserRepository $userRepository): Response
    {
        return $this->render('front/accueil/index.html.twig', [
            'projects' => $projectRepository->findAll(),
            'tasks' => $tasksRepository->findAll(), 
            'users' => $userRepository->findAll(),
        ]);
    }
}


