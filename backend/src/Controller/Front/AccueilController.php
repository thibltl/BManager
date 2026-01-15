<?php

namespace App\Controller\Front;

use App\Repository\ProjectRepository;
use App\Repository\TasksRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'front_accueil')]
    public function accueil(
        ProjectRepository $projectRepository,
        TasksRepository $tasksRepository,
        UserRepository $userRepository
    ): Response {
        
        $projects = $projectRepository->findAll();
        $tasks = $tasksRepository->findAll();
        $users = $userRepository->findAll();

        // -----------------------------
        // Construction des données pour le graphique
        // -----------------------------
        $projectsData = [];

        foreach ($tasks as $task) {
            $project = $task->getTaskProject()?->getProjectName() ?? 'Sans projet';
            $status = $task->getTaskStatus()?->getStatusName() ?? 'Inconnu';

            if (!isset($projectsData[$project])) {
                $projectsData[$project] = [
                    'En cours' => 0,
                    'Terminee' => 0,
                    'En retard' => 0,
                    'En attente' => 0,
                ];
            }

            if (isset($projectsData[$project][$status])) {
                $projectsData[$project][$status]++;
            }
        }

        return $this->render('front/accueil/index.html.twig', [
            'projects' => $projects,
            'tasks' => $tasks,
            'users' => $users,
            'projectsData' => $projectsData, // 🔥 indispensable pour le graphique
        ]);
    }
}
