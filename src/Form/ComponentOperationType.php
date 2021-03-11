<?php

namespace App\Form;

use App\Entity\ComponentOperation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComponentOperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enter', NumberType::class)
            ->add('exit', NumberType::class)
            ->add('modification', NumberType::class)
            ->add('production', NumberType::class)
            ->add('shipment', NumberType::class)
            ->add('state', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter state',
                    ])
                ]
            ])
            ->add('component', EntityType::class)
            ->add('Production', EntityType::class)
            ->add('Shipment', EntityType::class)
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComponentOperation::class,
        ]);
    }
}
