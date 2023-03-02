<?php

namespace App\Form;

use App\Entity\GpuOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GpuOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class,[
                'label' => false,
                'attr' => [
                    'class' => 'text-input',
                    'placeholder' => 'marka',
                    'maxlength' => 255
                ]
            ])
            ->add('model', TextType::class,[
                'label' => false,
                'attr' => [
                    'class' => 'text-input',
                    'placeholder' => 'model',
                    'maxlength' => 255
                ]
            ])
            ->add('manufacturer', TextType::class,[
                'label' => false,
                'attr' => [
                    'class' => 'text-input',
                    'placeholder' => 'producent',
                    'maxlength' => 255
                ]
            ])
            ->add('offer',OfferType::class, [
                'label' => false,  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GpuOffer::class,
        ]);
    }
}
