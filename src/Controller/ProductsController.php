<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\StockHistoric;
use App\Form\ProductsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'app_products_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager
            ->getRepository(Products::class)
            ->findAll();
        $listado=array();
        foreach ($products as $item) {
            $item->setStockFinal($entityManager);
            array_push($listado,$item);
        }

        return $this->render('products/index.html.twig', [
            'products' => $listado,
        ]);
    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTime());
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }
}
