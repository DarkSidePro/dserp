<?php

namespace App\Form;

use App\Entity\ComponentOperation;
use App\Entity\Production;
use App\Entity\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            /*->add('enter', NumberType::class, [
                'attr' => ['step' => '0.01', 'min' => '0']
            ])
            ->add('dispatch', NumberType::class, [
                'attr' => ['step' => '0.01', 'min' => '0']
            ])*/
            ->add('modification', NumberType::class, [
                'attr' => ['step' => '0.01', 'min' => '0']
            ])
            /*->add('production', NumberType::class, [
                'attr' => ['step' => '0.01', 'min' => '0']
            ])
            ->add('shipment', NumberType::class, [
                'attr' => ['step' => '0.01', 'min' => '0']
            ])
            /*->add('state', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter state',
                    ])
                ],
                'attr' => ['step' => '0.01', 'min' => '0']
            ])*/
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
