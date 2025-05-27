<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findWithSpecialPrice(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.special_price IS NOT NULL')
            ->andWhere('p.special_price > 0')
            ->getQuery()
            ->getResult();
    }

    public function findFilteredProducts($priceMin, $priceMax, $categoryId, $inStockOnly)
    {
        $qb = $this->createQueryBuilder('p');

        if ($priceMin !== null) {
            $qb->andWhere('p.price >= :priceMin')->setParameter('priceMin', $priceMin);
        }

        if ($priceMax !== null) {
            $qb->andWhere('p.price <= :priceMax')->setParameter('priceMax', $priceMax);
        }

        if ($categoryId) {
            $qb->join('p.category_id', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($inStockOnly) {
            $qb->andWhere('p.stock > 0');
        }

        return $qb->getQuery()->getResult();
    }
}
