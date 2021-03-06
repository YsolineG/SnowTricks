<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Doctrine\DBAL\Types\TextType;

class FigureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'style' => 'height: 200px'
                ]
            ])
            ->add('figureGroup', TextType::class, [
                'label' => false
            ])
            ->add('photos', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'label' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => UrlType::class,
                'allow_add' => true,
                'prototype' => true,
                'mapped' => false,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'attr' => [
                        'placeholder' => 'URL de la vidéo'
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
