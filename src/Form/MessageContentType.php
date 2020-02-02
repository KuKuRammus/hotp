<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'empty_data' => '',
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'mb-1',
                    'placeholder' => 'Very important message...',
                    'autocomplete' => 'off',
                    'autocorrect' => 'off',
                    'autocapitalize' => 'off',
                    'spellcheck' => 'off'
                ]
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Hide message'
            ]);
    }
}
