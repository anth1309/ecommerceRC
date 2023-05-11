<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs', name: 'admin_users_')]
#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository): Response

    {

        $users = $usersRepository->findBy([], ['lastname' => 'asc']);
        return $this->render('admin/users/index.html.twig', compact('users'));
    }
}
