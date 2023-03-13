<?php

namespace App\Form;

use App\Entity\GpuOffer;
use App\Service\GpuOfferService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GpuOfferType extends AbstractType
{
    private GpuOfferService $gpuOfferService;

    public function __construct(GpuOfferService $gpuOfferService)
    {
        $this->gpuOfferService = $gpuOfferService;    
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', ChoiceType::class,[
                'label' => false,
                'choices' => array_combine($this->gpuOfferService->GetBrands(), $this->gpuOfferService->GetBrands()),
                'attr' => [
                    'class' => 'select',
                ],
            ])
            ->add('model', ChoiceType::class,[
                'label' => false,
                'choices' => array_combine($this->gpuOfferService->GetModels(), $this->gpuOfferService->GetModels()),
                'attr' => [
                    'class' => 'select',
                ],
            ])
            ->add('manufacturer', ChoiceType::class,[
                'label' => false,
                'choices' => array_combine($this->gpuOfferService->GetManufacturers(), $this->gpuOfferService->GetManufacturers()),
                'attr' => [
                    'class' => 'select',
                ],
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
