<?php

namespace App\Controller;

use App\Entity\StockHistoric;
use App\Entity\Users;
use App\Entity\Products;
use App\Form\StockHistoricType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/historic')]
class StockHistoricController extends AbstractController
{
    #[Route('/', name: 'app_stock_historic_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stockHistorics = $entityManager
            ->getRepository(StockHistoric::class)
            ->findAll();

        return $this->render('stock_historic/index.html.twig', [
            'stock_historics' => $stockHistorics,
        ]);
    }

    #[Route('/new', name: 'app_stock_historic_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stockHistoric = new StockHistoric();
        $form = $this->createForm(StockHistoricType::class, $stockHistoric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager
            ->getRepository(Users::class)
            ->find(13);
            $product = $entityManager
            ->getRepository(Products::class)
            ->find(1);
            $stockHistoric->setUser($user);
            $stockHistoric->setProduct($product);
            $entityManager->persist($stockHistoric);
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_historic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_historic/new.html.twig', [
            'stock_historic' => $stockHistoric,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_historic_show', methods: ['GET'])]
    public function show(StockHistoric $stockHistoric): Response
    {
        return $this->render('stock_historic/show.html.twig', [
            'stock_historic' => $stockHistoric,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_historic_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StockHistoric $stockHistoric, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StockHistoricType::class, $stockHistoric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_historic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_historic/edit.html.twig', [
            'stock_historic' => $stockHistoric,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_historic_delete', methods: ['POST'])]
    public function delete(Request $request, StockHistoric $stockHistoric, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stockHistoric->getId(), $request->request->get('_token'))) {
            $entityManager->remove($stockHistoric);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_historic_index', [], Response::HTTP_SEE_OTHER);
    }
}
