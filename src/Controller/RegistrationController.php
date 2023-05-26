<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private $formFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
        private  EntityManagerInterface $em
    ) {
        $this->formFactory = $formFactory;
    }





    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UsersAuthenticator $authenticator,
        SendMailService $mail,
        JWTService $jwt
    ): Response {
        $user = new Users();
        //$form = $this->createForm(RegistrationFormType::class, $user);


        $formUser = $this->formFactory->createBuilder(RegistrationFormType::class, $user);
        $formUser->remove('roles');
        $form = $formUser->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);
            $this->em->persist($user);
            $this->em->flush();

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate(
                $header,
                $payload,
                $this->getParameter('app.jwtsecret')
            );

            $mail->send(
                'no-reply@rccham.com',
                $user->getEmail(),
                'Activation de votre compte sur le site RcCham',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository): Response
    {
        if (
            $jwt->isValid($token) && !$jwt->isExpired($token) &&
            $jwt->check($token, $this->getParameter('app.jwtsecret'))
        ) {

            $payload = $jwt->getPayload($token);
            //recupere le user
            $user = $usersRepository->find($payload['user_id']);
            //verif user existe et n a pas active son compte
            if ($user && !$user->getIs_Verified()) {
                $user->setIs_Verified(true);
                $this->em->flush($user);
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('profile_index');
            }
        }
        //ici pb ds le token
        $this->addFlash('danger', 'Le lien est expiré ou est invalide');
        return $this->redirectToRoute('app_login');
    }


    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecter por acceder a cette page!');
            return $this->redirectToRoute('app_login');
        }
        if ($user->getIs_Verified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà enregistré');
            return $this->redirectToRoute('profile_index');
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate(
            $header,
            $payload,
            $this->getParameter('app.jwtsecret')
        );

        $mail->send(
            'no-reply@monsite.com',
            $user->getEmail(),
            'Activation de votre compte sur Tony commerce',
            'register',
            compact('user', 'token')
        );
        $this->addFlash('success', 'Email de verification envoyé');
        return $this->redirectToRoute('profile_index');
    }
}
