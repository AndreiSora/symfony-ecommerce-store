<?php
// src/Controller/Admin/OrderCrudController.php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\AddressType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Orders')
            ->setPageTitle(Crud::PAGE_DETAIL, fn(Order $order) => sprintf('Order #%d Details', $order->getId()));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield AssociationField::new('customer_address', 'Customer')->onlyOnIndex();
        yield AssociationField::new('customer_address', 'Customer')->onlyOnDetail();


        yield CollectionField::new('customerAddress.addressId', 'Addresses')
            ->setEntryType(AddressType::class)
            ->onlyOnDetail();

        yield ChoiceField::new('status')
            ->setChoices([
                'New' => 'new',
                'Payment Pending' => 'payment_pending',
                'Processing' => 'processing',
                'Complete' => 'complete',
                'Shipped' => 'shipped',
            ])
            ->renderExpanded(false)
            ->renderAsNativeWidget();

        yield DateTimeField::new('createdAt')->setFormat('yyyy-MM-dd HH:mm')->onlyOnIndex();
        yield DateTimeField::new('createdAt')->setFormat('yyyy-MM-dd HH:mm')->onlyOnDetail();

        yield TextField::new('payment_method')->onlyOnIndex();
        yield TextField::new('payment_method')->onlyOnDetail();

        yield MoneyField::new('total', 'Total')
            ->setCurrency('USD')
            ->setStoredAsCents(false)
            ->formatValue(static function ($value, $entity) {
                return '$' . number_format($value, 2, ',', '.');
            })->onlyOnIndex();
        yield MoneyField::new('total', 'Total')
            ->setCurrency('USD')
            ->setStoredAsCents(false)
            ->formatValue(static function ($value, $entity) {
                return '$' . number_format($value, 2, ',', '.');
            })->onlyOnDetail();

        yield MoneyField::new('shippingTax', 'Shipping')
            ->setCurrency('USD')
            ->setStoredAsCents(false)
            ->onlyOnDetail()
            ->formatValue(static function ($value) {
                return '$' . number_format($value, 2, ',', '.');
            });

        yield CollectionField::new('orderItems', 'Items')
            ->onlyOnDetail()
            ->setTemplatePath('admin/fields/order_items.html.twig');
    }
}
