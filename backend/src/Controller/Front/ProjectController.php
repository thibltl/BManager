<?php

namespace App\Controller\Front;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/project')]
final class ProjectController extends AbstractController
{
    #[Route('/', name: 'front_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        // 🔒 Empêcher l'accès si l'utilisateur n'est pas connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $projects = $projectRepository->findForUser($this->getUser());

        return $this->render('front/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }


    #[Route('/new', name: 'front_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 🔥 Le créateur devient automatiquement membre du projet
            $project->addUser($this->getUser());

            // 🔒 Revalidation des membres (si tu as une logique d’équipe, à adapter)
            foreach ($project->getUsers() as $user) {
                if (!$user) {
                    throw $this->createAccessDeniedException('Utilisateur invalide.');
                }
            }

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('front_project_index');
        }

        return $this->render('front/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'front_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        // 🔒 Vérification d'accès : seuls les membres peuvent voir
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous ne faites pas partie de ce projet.');
        }

        return $this->render('front/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'front_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        // 🔒 Vérification d'accès
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $oldUsers = clone $project->getUsers();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 🔒 Revalidation des membres (si logique d’équipe, à adapter)
            foreach ($project->getUsers() as $user) {
                if (!$user) {
                    throw $this->createAccessDeniedException('Utilisateur invalide.');
                }
            }

            // 🔒 Empêcher qu’un projet se retrouve sans membres
            if ($project->getUsers()->count() === 0) {
                $this->addFlash('error', 'Un projet doit contenir au moins un membre.');
                // On restaure les anciens membres
                foreach ($oldUsers as $user) {
                    if (!$project->getUsers()->contains($user)) {
                        $project->addUser($user);
                    }
                }
                return $this->redirectToRoute('front_project_edit', ['id' => $project->getId()]);
            }

            // 🔒 Empêcher l’utilisateur connecté de se retirer s’il est le dernier membre
            if (
                $project->getUsers()->count() === 1 &&
                !$project->getUsers()->contains($this->getUser())
            ) {
                $this->addFlash('error', 'Vous ne pouvez pas vous retirer du projet car vous êtes le dernier membre.');
                // On restaure les anciens membres
                foreach ($oldUsers as $user) {
                    if (!$project->getUsers()->contains($user)) {
                        $project->addUser($user);
                    }
                }
                return $this->redirectToRoute('front_project_edit', ['id' => $project->getId()]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('front_project_index');
        }

        return $this->render('front/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'front_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        // 🔒 Vérification d'accès
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {

            // ⭐ Optionnel : empêcher la suppression si le projet contient des tâches
            // if (!$project->getTasks()->isEmpty()) {
            //     $this->addFlash('error', 'Vous ne pouvez pas supprimer un projet qui contient encore des tâches.');
            //     return $this->redirectToRoute('front_project_edit', ['id' => $project->getId()]);
            // }

            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_project_index');
    }
}
