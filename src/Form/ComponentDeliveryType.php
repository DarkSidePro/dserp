<?php

namespace App\Form;

use App\Entity\Component;
use App\Entity\ComponentOperation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComponentDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enter')
            ->add('component', EntityType::class, [
                'class' => Component::class,
                'choice_label' => 'component_name'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['btn btn-success']
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
