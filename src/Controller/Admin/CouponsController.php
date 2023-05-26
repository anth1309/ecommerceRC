<?php

namespace App\Controller\Admin;

use App\Entity\Coupons;
use App\Form\CouponsFormType;
use App\Repository\CouponsRepository;
use App\Repository\UsersRepository;
use App\Service\SendMailService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/coupons', name: 'admin_coupons_')]
#[IsGranted('ROLE_ADMIN')]
class CouponsController extends AbstractController
{
    public function __construct(
        private CouponsRepository $couponsRepository,
        private EntityManagerInterface $em,
        private  SendMailService $mail,
        private UsersRepository $usersRepository,
    ) {
    }


    #[Route('/', name: 'index')]
    public function index(): Response

    {
        $coupons = $this->couponsRepository->findBy([], ['created_at' => 'asc']);
        return $this->render('admin/coupons/index.html.twig', compact('coupons'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $coupon = new Coupons();
        $couponForm = $this->createForm(CouponsFormType::class, $coupon);
        $couponForm->handleRequest($request);
        if ($couponForm->isSubmitted() && $couponForm->isValid()) {
            $coupon->setCreatedAt(new DateTimeImmutable('now'));
            $this->em->persist($coupon);
            $this->em->flush();

            $this->addFlash('success', 'Coupons créer avec succès');
            return $this->redirectToRoute('admin_categories_index');
        }
        return $this->render('admin/coupons/form.html.twig', [
            'couponForm' => $couponForm->createView()
        ]);
    }


    #[Route("/admin/coupons/{id}", name: "send")]
    public function sendCoupon(
        $id
    ) {
        $users = $this->usersRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_USER"%')
            ->getQuery()
            ->getResult();
        $coupon = $this->couponsRepository->find($id);

        foreach ($users as $user) {
            $this->mail->send(
                'no-reply@rccham.com',
                $user->getEmail(),
                'Super promo',
                'coupon_email',
                compact('user', 'coupon')
            );
        }
        $this->addFlash('success', 'Le code promo a été envoyé à tous vos clients');
        return $this->redirectToRoute('admin_categories_index');
    }
}
