<?php

namespace App\Controller;

use App\Repository\UsersRepository;
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

    #[Route('/utilisateur', name: 'user')]
    public function profilUser(): Response
    {

        $user = $this->getUser();
        dump($user);
        return $this->render('profile/user.html.twig', compact('user'));
    }
}
