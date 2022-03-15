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
    #[Route('/{id}', name: 'app_stock_historic_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, $id): Response
    {
        $product = $entityManager
            ->getRepository(Products::class)
            ->find($id);
        $stockHistorics = $entityManager
            ->getRepository(StockHistoric::class)
            ->findBy(array('product' => $product));
        $listado=array();
        $stockFinal=$product->getStock();
        foreach ($stockHistorics as $item) {
            if($item->getStock() >= 0){
                $item->gestion='Añade';
            } else {
                $item->gestion='Elimina';
                $item->setStock($item->getStock()*-1);
            }
            $stockFinal=$stockFinal+$item->getStock();
            $item->stockGestion=$stockFinal;
            array_push($listado,$item);
        }

        return $this->render('stock_historic/index.html.twig', [
            'stock_historics' => $listado,
            'product' => $product,
            'stockFinal' => $stockFinal
        ]);
    }

    #[Route('/new/{id}', name: 'app_stock_historic_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $stockHistoric = new StockHistoric();
        $product = $entityManager
            ->getRepository(Products::class)
            ->find($id);
        $product->setStockFinal($entityManager);
        $stockHistoric->setProduct($product);
        $form = $this->createForm(StockHistoricType::class, $stockHistoric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockHistoric->setCreatedAt(new \DateTime());
            $user = $entityManager
            ->getRepository(Users::class)
            ->findAll();
            $stockHistoric->setUser($user[0]);
            $entityManager->persist($stockHistoric);
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_historic/new.html.twig', [
            'stock_historic' => $stockHistoric,
            'form' => $form,
            'product' => $product
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
