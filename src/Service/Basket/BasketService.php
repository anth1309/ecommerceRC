<?php

namespace App\Service\Basket;

use App\Entity\Coupons;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BasketService
{
    protected $productRepository;
    //private $flashBag;
    public function __construct(
        private RequestStack $requestStack,
        ProductsRepository $productRepository,
        private EntityManagerInterface $em,
        //FlashBagInterface $flashBag,

    ) {
        $this->productRepository = $productRepository;
        // $this->flashBag = $flashBag;
    }



    public function add(int $id)
    {

        $session = $this->requestStack->getSession();
        $bascket = $session->get('bascket', []);
        if (!empty($bascket[$id])) {
            $bascket[$id]++;
        } else {
            $bascket[$id] = 1;
        }
        $session->set('bascket', $bascket);
    }

    public function remove(int $id)
    {
        $session = $this->requestStack->getSession();
        $bascket = $session->get('bascket', []);
        if (!empty($bascket[$id])) {
            unset($bascket[$id]);
        } else {
            $bascket[$id] = 1;
        }
        $session->set('bascket', $bascket);
    }

    public function cut(int $id)
    {

        $session = $this->requestStack->getSession();
        $bascket = $session->get('bascket', []);
        if (!empty($bascket[$id])) {
            if ($bascket[$id] > 1) {
                $bascket[$id]--;
            } else {
                unset($bascket[$id]);
            }
        }

        $session->set('bascket', $bascket);
    }

    public function getFullBasket(): array
    {
        $session = $this->requestStack->getSession();
        $bascket = $session->get('bascket', []);
        $bascketWithData = [];
        foreach ($bascket as $id => $quantity) {
            $bascketWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        return $bascketWithData;
    }

    public function getTotal(): float
    {

        $bascketWithData = $this->getFullBasket();
        $total = 0;
        foreach ($bascketWithData as $item) {
            $totalBascketWithData = $item['product']->getPrice() * $item['quantity'];
            $total += $totalBascketWithData;
        }
        return $total;
    }


    public function removeAll()
    {
        $session = $this->requestStack->getSession();
        $session->remove("bascket");
        $session->remove('coupon');
    }

    public function removeCoupon()
    {
        $session = $this->requestStack->getSession();
        $session->remove('coupon');
    }


    public function addCoupon()
    {
        $session = $this->requestStack->getSession();
        $request = $this->requestStack->getCurrentRequest();
        $code = $request->request->get('code');
        $couponType = null;
        if (!$code) {
            // $this->flashBag->add('danger', 'Code promo manquant !');
        }
        $codeCoupon = $this->em->getRepository(Coupons::class)->findOneBy([
            'code' => $code,
            'is_valid' => true
        ]);
        if ($codeCoupon) {
            $session->set('coupon', $codeCoupon);
            $couponType = $codeCoupon->getCouponsTypes()->getId();
        } else {
            // $this->flashBag->add('danger', 'Code non valide !');
        }
        return $couponType;
    }
}
