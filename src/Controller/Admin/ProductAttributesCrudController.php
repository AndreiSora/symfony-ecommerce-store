<?php

namespace App\Controller\Admin;

use App\Entity\ProductAttributes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ProductAttributesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductAttributes::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('attribute', 'Attribute'),
            TextField::new('value', 'Value'),

            // This shows the relation to Products
            AssociationField::new('product_id', 'Products')
                ->setFormTypeOptions(['by_reference' => false]),
        ];
    }
}
