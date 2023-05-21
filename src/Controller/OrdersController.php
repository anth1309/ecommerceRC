<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdersController extends AbstractController
{
    #[Route('/orders', name: 'commande')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $orders = $em->getRepository(Orders::class)->findBy(['users' => $user], ['id' => 'desc']);

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
