<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Users $user, Request $request, EntityManagerInterface $em)
    {

        $userForm = $this->createForm(RegistrationFormType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès');

            return $this->redirectToRoute('admin_users_index');
        }
        return $this->render('admin/users/edit.html.twig', [
            'userForm' => $userForm->createView(),
            'users' => $user,
        ]);
    }
}
