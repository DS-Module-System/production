<?php

namespace App\Form\Production;

use App\Entity\Production\Production;
use App\Entity\Warehouse\Warehouse;
use App\Form\Core\DefaultForm\EditForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductionForm extends EditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description',
                'constraints' => [
                    new Length([
                        'max' => 1000,
                    ]),
                ],
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('product', EntityType::class, [
                'class' => \App\Entity\Product\Product::class,
                'choice_label' => 'name',
                'label' => 'product',
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'quantity',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => '0.00',
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'startDate',
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
                'empty_data' => '0000-00-00',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'endDate',
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
                'empty_data' => '0000-00-00',
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
                'placeholder' => 'chooseWarehouse',
                'attr' => [
                    'class' => 'select2',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Production::class,
            'translation_domain' => 'production',
        ]);
    }
} 