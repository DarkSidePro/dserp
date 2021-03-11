<?php

namespace App\Form;

use App\Entity\ProductOperation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enter')
            ->add('exit')
            ->add('modification')
            ->add('production')
            ->add('state')
            ->add('datestamp')
            ->add('product')
            ->add('Shipment')
            ->add('Production')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOperation::class,
        ]);
    }
}
