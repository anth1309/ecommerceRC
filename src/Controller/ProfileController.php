<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Entity\Orders;
use App\Repository\OrdersDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil', name: 'profile_')]

class ProfileController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private UsersRepository $usersRepository,
        private OrdersDetailsRepository $ordersDetails
    ) {
    }


    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $lastUser = $this->usersRepository->findOneBy([], ['id' => 'DESC']);

        return $this->render('profile/index.html.twig', compact('lastUser'));
    }


    #[Route('/orders', name: 'commande')]
    public function orders(): Response
    {
        $user = $this->getUser();
        $orders = $this->em->getRepository(Orders::class)->findBy(['users' => $user], ['created_at' => 'desc']);

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }


    #[Route('/orders/detail/{id}', name: 'commande_detail')]
    public function ordersDetails($id,): Response
    {
        $details = $this->ordersDetails->findBy(['orders' => $id]);

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
