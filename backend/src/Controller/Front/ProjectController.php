<?php

namespace App\Controller\Front;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/front/project', name: 'front_project_index')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $projects = $projectRepository->findForUser($this->getUser());

        return $this->render('front/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/front/project/{id}', name: 'front_project_show', requirements: ['id' => '\d+'])]
    public function show(Project $project): Response
    {
        $this->denyAccessUnlessGrantedToProject($project);

        return $this->render('front/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/front/project/new', name: 'front_project_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $project = new Project();

        $availableUsers = $userRepository->findByRole('ROLE_USER');

        $form = $this->createForm(ProjectType::class, $project, [
            'available_users' => $availableUsers,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project->addUser($this->getUser());

            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('front_project_index');
        }

        return $this->render('front/project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/front/project/{id}/edit', name: 'front_project_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGrantedToProject($project);

        $availableUsers = $userRepository->findAll();

        $form = $this->createForm(ProjectType::class, $project, [
            'available_users' => $availableUsers,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('front_project_index');
        }

        return $this->render('front/project/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/front/project/{id}/delete', name: 'front_project_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        Request $request,
        Project $project,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGrantedToProject($project);

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('front_project_index');
    }

    private function denyAccessUnlessGrantedToProject(Project $project): void
    {
        if (!$project->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }
    }
}
