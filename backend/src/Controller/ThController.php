<?php

namespace App\Controller;

use App\Entity\Th;
use App\Form\ThType;
use App\Repository\ThRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/th')]
final class ThController extends AbstractController
{
    #[Route(name: 'th_index', methods: ['GET'])]
    public function index(ThRepository $thRepository): Response
    {
        return $this->render('th/index.html.twig', [
            'ths' => $thRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_th_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $th = new Th();
        $form = $this->createForm(ThType::class, $th);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($th);
            $entityManager->flush();

            return $this->redirectToRoute('app_th_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('th/new.html.twig', [
            'th' => $th,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_th_show', methods: ['GET'])]
    public function show(Th $th): Response
    {
        return $this->render('th/show.html.twig', [
            'th' => $th,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_th_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Th $th, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ThType::class, $th);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_th_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('th/edit.html.twig', [
            'th' => $th,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_th_delete', methods: ['POST'])]
    public function delete(Request $request, Th $th, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$th->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($th);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_th_index', [], Response::HTTP_SEE_OTHER);
    }
}
