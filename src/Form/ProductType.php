<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter product name',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your product name should be at least {{ limit }} characters',
                        'max' => 255,
                        'maxMessage' => 'Your product name is too long',
                    ])
                ]
            ])
            ->add('animal', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'animal_name'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
