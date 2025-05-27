<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryController extends AbstractController
{
    #[Route('/category/create', name: 'category_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $category->setName('Phones');
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($category);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id ' . $category->getId());
    }

    #[Route('/category/view/{id}', name: 'category_view', methods: ['GET'])]
    public function view(int $id, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException("Category with ID $id not found.");
        }

        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Get filters from query parameters
        $filters = $request->query->all();

        // Get all products from the category (Doctrine Collection)
        $products = $category->getProducts();

        // Filter products by attributes if filters exist
        if (!empty($filters)) {
            $products = $products->filter(function ($product) use ($filters) {
                foreach ($filters as $filterKey => $filterValue) {
                    // Normalize filter value to array for easier matching
                    $filterValues = is_array($filterValue) ? $filterValue : [$filterValue];

                    // Get product attributes matching filterKey
                    $matchingAttrs = $product->getProductAttributes()->filter(function ($attr) use ($filterKey) {
                        return strtolower($attr->getAttribute()) === strtolower($filterKey);
                    });

                    if ($matchingAttrs->isEmpty()) {
                        // Product doesn't have this attribute at all
                        return false;
                    }

                    // Check if any attribute value matches one of the filter values
                    $matched = false;
                    foreach ($matchingAttrs as $attr) {
                        if (in_array($attr->getValue(), $filterValues, true)) {
                            $matched = true;
                            break;
                        }
                    }

                    if (!$matched) {
                        return false;
                    }
                }

                return true;
            });
        }

        // Prepare attributes list from all category products (for filters UI)
        $attributes = [];
        foreach ($category->getProducts() as $product) {
            foreach ($product->getProductAttributes() as $productAttribute) {
                $attrName = $productAttribute->getAttribute();
                $attrValue = $productAttribute->getValue();

                if (!isset($attributes[$attrName])) {
                    $attributes[$attrName] = [];
                }
                if (!in_array($attrValue, $attributes[$attrName], true)) {
                    $attributes[$attrName][] = $attrValue;
                }
            }
        }

        return $this->render('category/view.html.twig', [
            'currentCategory' => $category,
            'products' => $products,
            'categories' => $categories,
            'appliedFilters' => $filters,
            'attributes' => $attributes,
        ]);
    }
}
