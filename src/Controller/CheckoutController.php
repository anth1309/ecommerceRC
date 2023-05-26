<?php

namespace App\Controller;

use App\Entity\Coupons;
use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Entity\Products;
use App\Form\DelivryFormType;
use App\Repository\DelivrysAddressRepository;
use App\Repository\OrdersRepository;
use App\Repository\OrdersDetailsRepository;
use App\Repository\UsersRepository;
use App\Service\Basket\BasketService;
use App\Service\PdfService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    protected $ordersRepository;
    protected $ordersDetails;
    public function __construct(
        private EntityManagerInterface $em,
        private UsersRepository $usersRepository,
        private PdfService $pdfService,
        private BasketService $basketService,
        private RequestStack $requestStack,
        OrdersRepository $ordersRepository,
        OrdersDetailsRepository   $ordersDetails,
        private FormFactoryInterface $formFactory,
        private DelivrysAddressRepository $delivrysAddress


    ) {
        $this->ordersRepository = $ordersRepository;
        $this->ordersDetails = $ordersDetails;
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
        $adressAdd = $session->get('adressAdd');

        if ($couponId) {
            $totalDiscount = $session->get('totaldiscount');
            $coupon = $this->em->getRepository(Coupons::class)->find($couponId);
        }
        $order = (new Orders())
            ->setCoupons($coupon)
            ->setUsers($this->getUser())
            ->setReference($this->getUser()->getLastname() . '-' .  $dateString)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setTotal($total)
            ->setTotalDiscount($totalDiscount);
        $this->em->persist($order);
        $this->em->flush();
        $lastId = $this->ordersRepository->findOneBy([], ['id' => 'desc']);

        foreach ($bascket as $key => $value) {
            $productId = $key;
            $product = $this->em->getRepository(Products::class)->find($productId);
            $stock = $product->getStock();
            $ordersDetails = (new OrdersDetails())
                ->setOrders($lastId)
                ->setQuantity($value)
                ->setPrice($product->getPrice())
                ->setProducts($product);
            $this->em->persist($ordersDetails);
            $this->em->flush();
        }
        $user = $this->getUser();
        $orderSummary = $this->ordersRepository->findOneBy(['users' => $user->getId()], ['created_at' => 'DESC']);
        $lastIdOrders = $orderSummary->getId();
        $detailsInit = $this->em->getRepository(OrdersDetails::class)->findBy(['orders' => $lastIdOrders]);
        $delivryForm = $this->createForm(DelivryFormType::class);
        $session->set('details', $detailsInit);
        $details = $session->get('details');

        return $this->render(
            'payment/index.html.twig',
            compact('user', 'orderSummary', 'details', 'adressAdd')
        );
    }

    #[Route('/checkout/ajout-adresse', name: 'checkout_delivryAdress')]
    public function addDelivryAddress(Request $request)
    {
        $delivryForm = $this->createForm(DelivryFormType::class);
        $delivryForm->handleRequest($request);
        $user = $this->getUser();

        if ($delivryForm->isSubmitted() && $delivryForm->isValid()) {
            $delivryData = $delivryForm->getData();
            $delivryData->setUser($user);
            $this->em->persist($delivryData);
            $this->em->flush();
            $this->addFlash('success', 'Adresse de livraison ajoutée avec succès');
            return $this->redirectToRoute('checkout_delivryAdress');
        }
        $adressExist = $this->delivrysAddress->findBy(['user' => $user->getId()]);

        return $this->render(
            'payment/delivryAdd.html.twig',
            compact('delivryForm', 'adressExist')
        );
    }

    #[Route('/checkout/suppression-adresse/{id}', name: 'checkout_deleteAdress')]
    public function deletteDelivryAddress($id)
    {
        $adressDelete = $this->delivrysAddress->find($id);
        $this->em->remove($adressDelete);
        $this->em->flush();
        return $this->redirectToRoute(
            'checkout_delivryAdress'
        );
    }

    #[Route('/checkout/ajouter-adresse/{id}', name: 'checkout_addDelivry')]
    public function addNewDelivryAddress($id)
    {
        $adressAdd = $this->delivrysAddress->find($id);
        $session = $this->requestStack->getSession();
        $session = $this->requestStack->getSession();
        $session->set('adressAdd', $adressAdd);
        return $this->redirectToRoute(
            'checkout',
        );
    }
}
