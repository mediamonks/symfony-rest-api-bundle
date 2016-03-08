<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Constraint;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new Constraint\NotBlank,
                        new Constraint\Length(['min' => 3, 'max' => 255])
                    ]
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => [
                        new Constraint\NotBlank,
                        new Constraint\Email,
                        new Constraint\Length(['min' => 5, 'max' => 255])
                    ]
                ]
            )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false
            ]
        );
    }

    public function getName()
    {
        return '';
    }
}