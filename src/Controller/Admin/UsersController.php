<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs', name: 'admin_users_')]
#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $em,
        private OrdersRepository $orders,
        private UsersRepository $usersRepository,
    ) {
    }


    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $users = $this->usersRepository->findBy([], ['lastname' => 'asc']);
        return $this->render('admin/users/index.html.twig', compact('users'));
    }


    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Request $request, Users $user)
    {
        $formBuilderPartiel = $this->formFactory->createBuilder(RegistrationFormType::class, $user);
        $formBuilderPartiel->remove('plainPassword');
        $userFormPartiel = $formBuilderPartiel->getForm();
        $userFormPartiel->handleRequest($request);

        if ($userFormPartiel->isSubmitted() && $userFormPartiel->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'userFormPartiel' => $userFormPartiel->createView(),
            'users' => $user,
        ]);
    }


    #[Route('/detete/{id}', name: 'delete')]
    public function delete($id): Response
    {
        $userDelete = $this->usersRepository->find($id);
        $ordersUser = $this->orders->findBy(['users' => $id]);

        if ($ordersUser) {
            $this->addFlash('warning', 'Vous ne pouvez pas supprimer cet utilisateur car il a des commandes');
        } else {
            $this->em->remove($userDelete);
            $this->em->flush();
        }
        return $this->redirectToRoute('admin_users_index');
    }
}
