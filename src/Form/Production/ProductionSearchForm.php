<?php

namespace App\Form\Production;

use App\Entity\Warehouse\Warehouse;
use App\Form\Core\DefaultForm\SearchForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Product\Product;

class ProductionSearchForm extends SearchForm
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $router
    ) {
    } 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'name'
            ])
            ->add('product', EntityType::class, [
                'label' => 'product',
                'class' => Product::class,
                'required' => false,
                'placeholder' => 'allProducts',
                'choice_label' => function (Product $entity) {
                    return $entity->getName();
                },
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add('startDateFrom', DateType::class, [
                'required' => false,
                'label' => 'startDateFrom',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
            ])
            ->add('startDateTo', DateType::class, [
                'required' => false,
                'label' => 'startDateTo',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
            ])
            ->add('endDateFrom', DateType::class, [
                'required' => false,
                'label' => 'endDateFrom',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
            ])
            ->add('endDateTo', DateType::class, [
                'required' => false,
                'label' => 'endDateTo',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
            ])
            ->add('warehouse', EntityType::class, [
                'class' => Warehouse::class,
                'label' => 'warehouse',
                'required' => false,
                'choice_label' => function (Warehouse $entity) {
                    return $entity->getName();
                },
                'placeholder' => 'allWarehouses',
                'attr' => [
                    'class' => 'select2',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $resolver->setDefault('action', $this->router->generate($request->get('_route'),
                array_merge($request->get('_route_params'), ['page'=>1])));
        }
        $resolver->setDefault('translation_domain', 'production');
    }
} 