<?php

namespace App\Controller;

use App\Entity\Panne;
use App\Form\PanneType;
use App\Repository\PanneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/technicien/pannes')]
final class PanneController extends AbstractController
{
    #[Route(name: 'panne_index', methods: ['GET'])]
    public function index(PanneRepository $panneRepository): Response
    {
        return $this->render('panne/index.html.twig', [
            'pannes' => $panneRepository->findBy(['technicien' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'panne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panne = new Panne();
        $form = $this->createForm(PanneType::class, $panne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $panne->setTechnicien($this->getUser());
            $entityManager->persist($panne);
            $entityManager->flush();

            return $this->redirectToRoute('panne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panne/new.html.twig', [
            'panne' => $panne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'panne_show', methods: ['GET'])]
    public function show(Panne $panne): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TECHNICIEN');

        if ($panne->getTechnicien() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('panne/show.html.twig', [
            'panne' => $panne,
        ]);
    }

    #[Route('/{id}/edit', name: 'panne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panne $panne, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TECHNICIEN');

        if ($panne->getTechnicien() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PanneType::class, $panne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('panne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panne/edit.html.twig', [
            'panne' => $panne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'panne_delete', methods: ['POST'])]
    public function delete(Request $request, Panne $panne, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TECHNICIEN');

        if ($panne->getTechnicien() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$panne->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($panne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panne_index', [], Response::HTTP_SEE_OTHER);
    }
}
