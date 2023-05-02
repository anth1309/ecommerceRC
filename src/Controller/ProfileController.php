<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/profil', name: 'profile_')]



class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository): Response
    {
        // return $this->render('profile/index.html.twig', [
        //     'controller_name' => 'Profil de l\'utilisateur',
        // ]);

        $lastUser = $usersRepository->findOneBy([], ['id' => 'DESC']);
        return $this->render('profile/index.html.twig', compact('lastUser'));
    }

    #[Route('/commandes', name: 'orders')]
    public function orders(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Commande de l\'utilisateur',
        ]);
    }

    #[Route('/produits', name: 'products')]
    public function products(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Produits de l\'utilisateur',
        ]);
    }
}
