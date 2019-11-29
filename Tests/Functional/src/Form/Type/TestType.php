<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                        new NotBlank,
                        new Length(['min' => 3, 'max' => 255])
                    ]
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank,
                        new Email,
                        new Length(['min' => 5, 'max' => 255])
                    ]
                ]
            )
            ;
    }
}
