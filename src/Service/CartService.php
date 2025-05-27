<?php

// src/Service/CartService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProductRepository;

class CartService
{
    private RequestStack $requestStack;
    private ProductRepository $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;

        $this->productRepository = $productRepository;
    }

    public function add(int $productId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $cart[$productId] = ($cart[$productId] ?? 0) + 1;
        $session->set('cart', $cart);
    }

    public function remove(int $productId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        $session->set('cart', $cart);
    }

    public function decrease(int $productId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$productId]) && $cart[$productId] >= 2) {
            $cart[$productId]--;
            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }
        }

        $session->set('cart', $cart);
    }

    public function increase(int $productId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $cart[$productId] = ($cart[$productId] ?? 0) + 1;
        $session->set('cart', $cart);
    }

    public function getCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $detailedCart = [];

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $detailedCart[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return $detailedCart;
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['quantity'];
        }

        return $total;
    }

    public function clear(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }
}
