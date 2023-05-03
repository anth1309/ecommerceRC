<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BascketControlllerController extends AbstractController
{
    public function __construct(private ProductsRepository $productsRepository)
    {
    }
    #[Route('/panier', name: 'bascket_index')]
    public function index(SessionInterface $session): Response
    {
        $bascket = $session->get('bascket');
        $bascketWithData = [];
        foreach ($bascket as $id => $quantity) {
            $bascketWithData[] = [
                'product' => $this->productsRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;
        foreach ($bascketWithData as $item) {
            $totalBascketWithData = $item['product']->getPrice() * $item['quantity'];
            $total += $totalBascketWithData;
        }
        return $this->render('bascket/index.html.twig', [
            'bascketsWithData' => $bascketWithData,
            'total' => $total / 100
        ]);
    }

    #[Route("/panier/ajouter/{id}", name: "bascket_add")]
    public function add($id, SessionInterface $session)
    {
        $bascket = $session->get('bascket', []);
        if (!empty($bascket[$id])) {
            $bascket[$id]++;
        } else {
            $bascket[$id] = 1;
        }
        $session->set('bascket', $bascket);
    }
}
