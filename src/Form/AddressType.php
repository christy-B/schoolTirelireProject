<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'nom de l\'adresse',
                'attr' => [
                    'placeholder' => 'Saisissez un nom'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'votre nom',
                'attr' => [
                    'placeholder' => 'Saisissez votre nom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'votre penom',
                'attr' => [
                    'placeholder' => 'Saisissez votre prenom'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'votre entreprise',
                'required'=>false,
                'attr' => [
                    'placeholder' => 'Saisissez le nom de votre entreprise'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'votre adresse',
                'attr' => [
                    'placeholder' => 'Saisissez votre adresse'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'votre postal',
                'attr' => [
                    'placeholder' => 'Saisissez votre postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'votre ville',
                'attr' => [
                    'placeholder' => 'Saisissez votre ville'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'votre pays',
                'attr' => [
                    'placeholder' => 'Choisissez votre pays'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'votre telephone',
                'attr' => [
                    'placeholder' => 'Saisissez votre telephone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
