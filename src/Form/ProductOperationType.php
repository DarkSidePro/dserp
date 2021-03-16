<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Production;
use App\Entity\ProductOperation;
use App\Entity\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enter')
            ->add('dispatch')
            ->add('modification')
            ->add('production')
            ->add('state')
            ->add('production_id', EntityType::class, [
                'class' => Production::class,
                'choice_label' => 'id'
            ])
            ->add('shipment_id', EntityType::class, [
                'class' => Shipment::class,
                'choice_label' => 'id'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOperation::class,
        ]);
    }
}
