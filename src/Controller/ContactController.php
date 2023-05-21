<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, SendMailService $mail)
    {
        $user = $this->getUser();
        $formContact = $this->createForm(ContactFormType::class);
        $formContact->handleRequest($request);

        if ($formContact->isSubmitted() && $formContact->isValid()) {
            $contactFormData = $formContact->getData();
            $subject = 'Demande de contact sur votre site de ' .  $user->getLastname();
            $mail->send(
                $user->getEmail(),
                'no-reply@monsite.com',
                $subject,
                'contact',
                compact('user', 'contactFormData')
            );

            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('main');
        }

        return $this->render('admin/users/contact.html.twig', [
            'formContact' => $formContact->createView()
        ]);
    }
}
