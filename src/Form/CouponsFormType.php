<?php

namespace App\Form;

use App\Entity\Coupons;
use App\Entity\CouponsTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CouponsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Code'
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Description',

            ])
            ->add('discount', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Remise'
            ])
            ->add('max_usage', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nombre d utilisation'
            ])
            ->add('validity', DateType::class, [
                'widget' => 'single_text',

                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Fin de validité'
            ])
            ->add('is_valid', CheckboxType::class, [

                'label' => 'Reduction en cours validité'
            ])

            ->add('coupons_types', EntityType::class, [
                'class' => CouponsTypes::class,
                'choice_label' => 'name',
                'label' => 'Types de coupons',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coupons::class,
        ]);
    }
}
