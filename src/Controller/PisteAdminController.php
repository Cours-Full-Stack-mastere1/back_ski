<?php

namespace App\Controller;

use App\Entity\Piste;
use App\Form\PisteType;
use App\Repository\PisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/piste/admin')]
class PisteAdminController extends AbstractController
{
    #[Route('/', name: 'app_piste_admin_index', methods: ['GET'])]
    public function index(PisteRepository $pisteRepository): Response
    {
        return $this->render('piste_admin/index.html.twig', [
            'pistes' => $pisteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_piste_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $piste = new Piste();
        $form = $this->createForm(PisteType::class, $piste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($piste);
            $entityManager->flush();

            return $this->redirectToRoute('app_piste_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('piste_admin/new.html.twig', [
            'piste' => $piste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_piste_admin_show', methods: ['GET'])]
    public function show(Piste $piste): Response
    {
        return $this->render('piste_admin/show.html.twig', [
            'piste' => $piste,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_piste_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Piste $piste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PisteType::class, $piste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_piste_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('piste_admin/edit.html.twig', [
            'piste' => $piste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_piste_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Piste $piste, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$piste->getId(), $request->request->get('_token'))) {
            $entityManager->remove($piste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_piste_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
