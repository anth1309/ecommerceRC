<?php

namespace App\Controller;

use App\Entity\Coupons;
use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Entity\Products;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use App\Service\Basket\BasketService;
use App\Service\PdfService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    protected $ordersRepository;
    private $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager,
        private UsersRepository $usersRepository,
        private PdfService $pdfService,
        private BasketService $basketService,
        private RequestStack $requestStack,
        OrdersRepository $ordersRepository
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->entityManager = $entityManager;
    }


    #[Route('/checkout', name: 'checkout')]
    public function index(Request $request): Response
    {
        $date = new DateTimeImmutable();
        $dateString = $date->format('Y-m-d H:i:s');
        $session = $this->requestStack->getSession();
        $bascket = $session->get('bascket', []);
        $total = $this->basketService->getTotal();
        $coupon = null;
        $totalDiscount = null;
        $couponId = $session->get('coupon', []);

        if ($couponId) {
            $totalDiscount = $session->get('totaldiscount');
            $coupon = $this->entityManager->getRepository(Coupons::class)->find($couponId);
        }
        $order = (new Orders())
            ->setCoupons($coupon)
            ->setUsers($this->getUser())
            ->setReference($this->getUser()->getLastname() . '-' .  $dateString)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setTotal($total)
            ->setTotalDiscount($totalDiscount);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $lastId = $this->ordersRepository->findOneBy([], ['id' => 'desc']);

        foreach ($bascket as $key => $value) {
            $productId = $key;
            $product = $this->entityManager->getRepository(Products::class)->find($productId);
            $stock = $product->getStock();
            $ordersDetails = (new OrdersDetails())
                ->setOrders($lastId)
                ->setQuantity($value)
                ->setPrice($product->getPrice())
                ->setProducts($product);
            $product->setstock($stock - $value);
            $this->entityManager->persist($ordersDetails);
            $this->entityManager->flush();
        }

        $this->basketService->removeAll();
        $this->addFlash('success', 'Félicitation pour votre achat, nous traitons votre commande sous les plus brefs délais');
        return $this->redirectToRoute('main');
    }
}
