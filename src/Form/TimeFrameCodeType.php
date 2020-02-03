<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimeFrameCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'empty_data' => '',
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'ABC12',
                    'class' => 'mb-1',
                    'maxlength' => 5,
                    'size' => 5
                ]
            ])
            ->add('check', SubmitType::class, [
                'label' => 'Get message content'
            ]);
    }
}
