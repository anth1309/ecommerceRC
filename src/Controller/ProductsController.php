<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produits', name: 'products_')]
#[IsGranted('ROLE_USER')]
class ProductsController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('products/index.html.twig');
    }


    #[Route('/{slug}', name: 'details')]
    public function details(Products $product): Response
    {
        return $this->render(
            'products/details.html.twig',
            compact('product')
        );
    }
}
