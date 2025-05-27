<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ProductAttributesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('short_description', TextType::class, [
                'label' => 'Short Description',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'currency' => '$',
                'attr' => ['class' => 'form-control']
            ])
            ->add('special_price', MoneyType::class, [
                'label' => 'Special Price',
                'currency' => '$',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('productAttributes', CollectionType::class, [
                'label' => 'Attributes',
                'entry_type' => ProductAttributesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => ['class' => 'product-attributes-collection']
            ])
            ->add('category_id', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categories',
                'multiple' => true,     // multiple selection
                'expanded' => false,    // set to true for checkboxes instead of select box
                'attr' => ['class' => 'form-select', 'multiple' => 'multiple'], // Bootstrap 5 multi-select styling
                'by_reference' => false, // important for ManyToMany collections
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create Product',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
