<?php
// src/Controller/Admin/AdminController.php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\PaymentMethod;
use App\Entity\ShippingMethod;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Total counts
        $totalProducts = $this->entityManager->getRepository(Product::class)->count([]);
        $totalOrders = $this->entityManager->getRepository(Order::class)->count([]);
        $totalCustomers = $this->entityManager->getRepository(Customer::class)->count([]);

        // Total revenue
        $totalRevenue = (float) $this->entityManager->createQuery(
            'SELECT SUM(o.total) FROM App\Entity\Order o'
        )->getSingleScalarResult() ?? 0;

        return $this->render('admin/dashboard.html.twig', [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/logo.png" alt="My Store" style="height:32px;margin-right:8px;">Admin Panel');
    }

    public function configureMenuItems(): iterable
    {
        // link back to dashboard (redirects to product list)
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Manage entities
        yield MenuItem::linkToCrud('Products', 'fas fa-box', Product::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Orders', 'fas fa-shopping-cart', Order::class);
        yield MenuItem::linkToCrud('Customers', 'fas fa-users', Customer::class);

        yield MenuItem::linkToCrud('Payment Methods', 'fas fa-credit-card', PaymentMethod::class);
        yield MenuItem::linkToCrud('Shipping Methods', 'fas fa-shipping-fast', ShippingMethod::class);

        // Logout button
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}
