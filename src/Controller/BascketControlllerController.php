<?php

namespace App\Controller;

use App\Entity\Coupons;
use App\Repository\ProductsRepository;
use App\Service\Basket\BasketService;
use App\Service\PdfService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BascketControlllerController extends AbstractController
{

    public function __construct(
        private ProductsRepository $productsRepository,
        private BasketService $basketService,
        private RequestStack $requestStack,
        private EntityManagerInterface $em,

    ) {
    }


    #[Route('/panier', name: 'bascket_index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {

        $session = $this->requestStack->getSession();
        $codeDescription = $session->get('coupon', []);


        $bascketWithData = $this->basketService->getFullBasket();
        $total = $this->basketService->getTotal();
        $newTotal = 0;
        if ($codeDescription) {
            if ($codeDescription->getCouponsTypes()->getId() == 1) {
                $newTotal = $total * 0.8;
            } elseif ($codeDescription->getCouponsTypes()->getId() == 2) {
                $newTotal = $total - 10000;
            }
            $session->set('totaldiscount',  $newTotal);
        }


        return $this->render('bascket/index.html.twig', [
            'bascketsWithData' => $bascketWithData,
            'total' => $total / 100,
            'codeDescription' => $codeDescription,
            'newTotal' => $newTotal / 100,
        ]);
    }


    #[Route("/panier/ajouter/{id}", name: "bascket_add")]
    #[IsGranted('ROLE_USER')]
    public function add($id)
    {
        $this->basketService->add($id);
        return $this->redirectToRoute('bascket_index');
    }


    #[Route("/panier/moins-un/{id}", name: "bascket_cut")]
    #[IsGranted('ROLE_USER')]
    public function cut($id)
    {
        $this->basketService->cut($id);
        return $this->redirectToRoute('bascket_index');
    }


    #[Route("/panier/supprimer/{id}", name: "bascket_remove")]
    #[IsGranted('ROLE_USER')]
    public function remove($id)
    {
        $this->basketService->remove($id);
        return $this->redirectToRoute('bascket_index');
    }


    #[Route("/panier/supprimer", name: "bascket_removeAll")]
    #[IsGranted('ROLE_USER')]
    public function removeAll()
    {
        $this->basketService->removeAll();
        return $this->redirectToRoute('bascket_index');
    }


    #[Route("/panier/supp/coupon", name: "bascket_deleteCoupon")]
    #[IsGranted('ROLE_USER')]
    public function removeCoupon()
    {
        $this->basketService->removeCoupon();
        return $this->redirectToRoute('bascket_index');
    }


    #[Route("/panier/coupon", name: "order_add_coupon", methods: ["POST"])]
    #[IsGranted('ROLE_USER')]
    public function addCouponAction()
    {
        $this->basketService->addCoupon();

        return $this->redirectToRoute('bascket_index');
    }




    // #[Route("/panier/pdf", name: "bascket_pdf")]
    // #[IsGranted('ROLE_USER')]
    // public function createPdf(PdfService $pdfService)
    // {
    //     $bascketWithData = $this->basketService->getFullBasket();
    //     $total = $this->basketService->getTotal();
    //     $html = $this->renderView('bascket/details.html.twig', [
    //         'bascketsWithData' => $bascketWithData,
    //         'total' => $total / 100
    //     ]);
    //     $pdfService->showPdfFile($html);
    // }


    // $session = $this->requestStack->getSession();
    // $codeDescription = $session->get('bascket', []);


    // $content = json_decode($request->getContent(), true);
    // $recipientName = $content['data'];
    // $code = $em->getRepository(Coupons::class)->findOneBy([
    //     'code' => $recipientName,
    //     'is_valid' => true
    // ]);

    // if ($code != null) {
    //     $codeDescription = $code->getDescription();
    // }
    // $session->set('bascket', $codeDescription);



}
