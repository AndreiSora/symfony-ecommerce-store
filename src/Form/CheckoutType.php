<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\PaymentMethod;
use App\Entity\ShippingMethod;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    EmailType,
    TelType,
    ChoiceType
};

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('phone', TelType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('country', ChoiceType::class, [
                'choices' => $options['countries'],
                'label' => 'Country',
                'placeholder' => 'Choose your country',
            ])
            ->add('zip', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('county', TextType::class, ['required' => false])
            ->add('city', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('streetAddress', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('paymentMethod', EntityType::class, [
                'class' => PaymentMethod::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('shippingMethod', EntityType::class, [
                'class' => ShippingMethod::class,
                'choice_label' => function (ShippingMethod $method) {
                    $priceFormatted = number_format($method->getPrice(), 2);
                    // Replace dot and decimals with <sup>
                    return $method->getName() . ' - $' . preg_replace('/\.(\d{2})$/', '<sup>$1</sup>', $priceFormatted);
                },
                'expanded' => true,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined([
            'countries',
            'paymentMethods',
            'shippingMethods',
        ]);
    }
}
