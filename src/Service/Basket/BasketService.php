<?php

namespace App\Service\Basket;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BasketService
{
    protected $productRepository;
    public function __construct(
        private RequestStack $requestStack,
        ProductsRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
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
        $bascket = $session->get('bascket', []);

        $session->remove("bascket");
    }
}
