<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\OrdersDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil', name: 'profile_')]



class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_USER')]
    public function index(UsersRepository $usersRepository): Response
    {
        $lastUser = $usersRepository->findOneBy([], ['id' => 'DESC']);
        return $this->render('profile/index.html.twig', compact('lastUser'));
    }


    #[Route('/orders', name: 'commande')]
    public function orders(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $orders = $em->getRepository(Orders::class)->findBy(['users' => $user], ['created_at' => 'desc']);
        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }


    #[Route('/orders/detail/{id}', name: 'commande_detail')]
    public function ordersDetails($id, OrdersDetailsRepository $ordersDetails): Response
    {
        $details = $ordersDetails->findBy(['orders' => $id]);
        return $this->render(
            'orders/detailCommande.html.twig',
            compact('details')

        );
    }






    #[Route('/utilisateur', name: 'user')]
    public function profilUser(): Response
    {
        $user = $this->getUser();
        return $this->render('profile/user.html.twig', compact('user'));
    }
}
