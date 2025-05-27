<?php

namespace App\Controller;

use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Category;
use App\Repository\ProductRepository;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function ajaxAddToCart(int $id, CartService $cartService, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Product not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($product->getStock() == 0) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Product is not in stock.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $cartService->add($id);

        return new JsonResponse([
            'success' => true,
            'productCount' => $cartService->getTotalQuantity(),
        ]);
    }

    #[Route('/cart', name: 'cart_view')]
    public function viewCart(CartService $cartService, EntityManagerInterface $entityManager)
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('cart/view.html.twig', [
            'categories' => $categories,
            'cart' => $cartService->getCart(),
            'shipping' => 5.00,
        ]);
    }

    #[Route('/cart/increase/{id}', name: 'cart_increase', methods: ['POST'])]
    public function increase(int $id, CartService $cartService): JsonResponse
    {
        $cartService->increase($id);

        return new JsonResponse([
            'success'        => true,
            'cartItemCount'  => $cartService->getTotalQuantity(),
        ]);
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease', methods: ['POST'])]
    public function decrease(int $id, CartService $cartService): JsonResponse
    {
        $cartService->decrease($id);

        return new JsonResponse([
            'success'        => true,
            'cartItemCount'  => $cartService->getTotalQuantity(),
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(int $id, CartService $cartService): JsonResponse
    {
        $cartService->remove($id);

        return new JsonResponse([
            'success'        => true,
            'cartItemCount'  => $cartService->getTotalQuantity(),
        ]);
    }
}
