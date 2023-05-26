<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Service\Basket\BasketService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private $stripeSecretKey;
    public function __construct(
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $url,
        private BasketService $basketService,
    ) {
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->stripeSecretKey = $_ENV['STRIKE_SECRET_KEY_TEST'];
        } else {
            $this->stripeSecretKey = $_ENV['STRIKE_SECRET_KEY_LIVE'];
        }
    }


    #[Route('/order/create-session-stripe/{reference}', name: 'payement_stripe')]
    public function stripeCheckout($reference): RedirectResponse
    {
        $order = $this->em->getRepository(Orders::class)->findOneBy(['reference' => $reference]);
        if (!$order) {
            return $this->redirectToRoute('bascket_index');
        }
        if ($order->getTotalDiscount()) {
            $price = $order->getTotalDiscount();
        } else {
            $price = $order->getTotal();
        };
        $orderStripe = [];
        $orderStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $price,
                'product_data' => [
                    'name' => $order->getReference()
                ]
            ],
            'quantity' => 1,
        ];
        Stripe::setApiKey($this->stripeSecretKey);

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $orderStripe
            ],
            'mode' => 'payment',
            'success_url' => $this->url->generate(
                'payment_success',
                ['reference' => $order->getReference()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->url->generate(
                'payment_cancel',
                ['reference' => $order->getReference()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
        $order->setSrtipeSessionId($checkout_session->id);

        $this->em->persist($order);
        $this->em->flush();
        return new RedirectResponse($checkout_session->url);
    }


    #[Route('/order/success/{reference}', name: 'payment_success')]
    public function stripeSuccess($reference): Response
    {
        $order = $this->em->getRepository(Orders::class)->findOneBy(['reference' => $reference]);

        $lastIdOrders = $order->getId();
        $details = $this->em->getRepository(OrdersDetails::class)->findBy(['orders' => $lastIdOrders]);

        foreach ($details as $detail) {
            $product = $detail->getProducts();
            $quantity = $detail->getQuantity();
            $stock = $product->getStock();

            $product->setstock($stock - $quantity);
            $this->em->persist($product);
            $this->em->flush();
        }

        $order->setIsPaid(true);
        $this->em->persist($order);
        $this->em->flush();
        $this->basketService->removeAll();
        return $this->render('/payment/success.html.twig');
    }


    #[Route('/order/cancel/{reference}', name: 'payment_cancel')]
    public function stripeCancel($reference): Response
    {
        $order = $this->em->getRepository(Orders::class)->findOneBy(['reference' => $reference]);
        $order->setIsPaid(false);
        $this->em->persist($order);
        $this->em->flush();
        return $this->render('/payment/cancel.html.twig');
    }
}
