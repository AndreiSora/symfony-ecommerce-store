<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductAttributesType;
use App\Form\ProductImageType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Products');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield IdField::new('sku');
        yield TextField::new('name');
        yield TextareaField::new('description');
        yield TextField::new('short_description')->onlyOnForms();
        yield IntegerField::new('stock');
        yield BooleanField::new('salable');

        yield MoneyField::new('price')
            ->setCurrency('USD')
            ->setStoredAsCents(false);

        yield MoneyField::new('special_price')
            ->setCurrency('USD')
            ->setStoredAsCents(false)
            ->setRequired(false);

        yield AssociationField::new('category_id', 'Categories')
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnForms();

        yield CollectionField::new('productAttributes', 'Attributes')
            ->setEntryType(ProductAttributesType::class)
            ->allowAdd()
            ->allowDelete()
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnForms();

        yield CollectionField::new('images', 'Gallery Images')
            ->setEntryType(ProductImageType::class)
            ->allowAdd()
            ->allowDelete()
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnForms();
    }
}
