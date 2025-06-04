<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Form\AddressType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class CustomerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name');
        yield TextField::new('email');
        yield TextField::new('phone');

        // Embedded collection of AddressType on forms and detail
        yield CollectionField::new('address_id', 'Addresses')
            ->setEntryType(AddressType::class)
            ->allowAdd()
            ->allowDelete()
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnForms();

        yield CollectionField::new('address_id', 'Addresses')
            ->setEntryType(AddressType::class)
            ->onlyOnDetail();
    }
}
