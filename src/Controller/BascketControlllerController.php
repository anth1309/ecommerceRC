<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use App\Service\Basket\BasketService;
use App\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BascketControlllerController extends AbstractController
{
    public function __construct(
        private ProductsRepository $productsRepository,
        private BasketService $basketService
    ) {
    }
    #[Route('/panier', name: 'bascket_index')]
    public function index(): Response
    {
        $bascketWithData = $this->basketService->getFullBasket();
        $total = $this->basketService->getTotal();
        return $this->render('bascket/index.html.twig', [
            'bascketsWithData' => $bascketWithData,
            'total' => $total / 100
        ]);
    }


    #[Route("/panier/ajouter/{id}", name: "bascket_add")]
    public function add($id)
    {
        $this->basketService->add($id);
        return $this->redirectToRoute('bascket_index');
    }

    #[Route("/panier/moins-un/{id}", name: "bascket_cut")]
    public function cut($id)
    {
        $this->basketService->cut($id);
        return $this->redirectToRoute('bascket_index');
    }

    #[Route("/panier/supprimer/{id}", name: "bascket_remove")]
    public function remove($id)
    {
        $this->basketService->remove($id);
        return $this->redirectToRoute('bascket_index');
    }

    #[Route("/panier/supprimer", name: "bascket_removeAll")]
    public function removeAll()
    {
        $this->basketService->removeAll();
        return $this->redirectToRoute('bascket_index');
    }

    // #[Route("/panier/pdf", name: "bascket_pdf")]
    // public function createPdf(PdfService $pdfService)
    // {
    //     $html = $this->render('bascket/index.html.twig');
    //     $pdfService->showPdfFile($html);
    // }
}
