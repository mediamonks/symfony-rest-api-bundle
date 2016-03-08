<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\HttpKernel\Kernel;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $textField = 'text';
        if (version_compare(Kernel::VERSION, '2.8.0', '>=')) {
            $textField = 'Symfony\Component\Form\Extension\Core\Type\TextType';
        }

        $builder
            ->add(
                'name',
                $textField,
                [
                    'constraints' => [
                        new Constraint\NotBlank,
                        new Constraint\Length(['min' => 3, 'max' => 255])
                    ]
                ]
            )
            ->add(
                'email',
                $textField,
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