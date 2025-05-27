<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Entity\Product;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $discountedProducts = $entityManager->getRepository(Product::class)->findWithSpecialPrice();
        
        return $this->render('index.html.twig', [
            'categories' => $categories,
            'discountedProducts' => $discountedProducts
        ]);
    }
}
