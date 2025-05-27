<?php

namespace App\Form;

use App\Entity\ProductAttributes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAttributesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attribute', TextType::class, [
                'label' => 'Attribute',
                'attr' => ['class' => 'form-control']
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductAttributes::class,
        ]);
    }
}