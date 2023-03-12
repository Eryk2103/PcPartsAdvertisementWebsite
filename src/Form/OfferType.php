<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class,[
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'placeholder' => 'tytuł',
                'maxlength' => 255
            ]
        ])
        ->add('description', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'textarea-input',
                'placeholder' => 'opis',
            ]
        ])
        ->add('price', NumberType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'placeholder' => 'cena'

            ]
        ])
        ->add('img', FileType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
            ],
            'mapped' => false,
            'required' => false
        ])
        ->add('location', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'placeholder' => 'miejscowość',
                'maxlength' => 100
            ]
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
