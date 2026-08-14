<?php

namespace App\Form\Production;

use App\Entity\Production\ProductionMaterial;
use App\Entity\Warehouse\Warehouse;
use App\Form\Core\DefaultForm\EditForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductionMaterialForm extends EditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('material', EntityType::class, [
                'class' => \App\Entity\Product\Product::class,
                'choice_label' => 'name',
                'label' => 'material',
                'required' => true,
                'constraints' => [new NotBlank()],
                'placeholder' => 'chooseMaterial',
                'attr' => ['class' => 'select2'],
            ]) 
            ->add('quantity', NumberType::class, [
                'label' => 'quantity',
                'required' => true,
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'attr' => [
                    'step' => '0.01',
                    'min' => '0.01',
                    'placeholder' => '0.00',
                ],
            ])
            ->add('warehouse', EntityType::class, [
                'class' => Warehouse::class,
                'label' => 'warehouse',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'choice_label' => function (Warehouse $entity) {
                    return $entity->getName();
                },
                'placeholder' => 'chooseRawMaterialWarehouse',
                'attr' => [
                    'class' => 'select2',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductionMaterial::class,
            'translation_domain' => 'production',
        ]);
    }
} 