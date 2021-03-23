<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title' , TextType::class, [
                'attr' => [
                    'placeholder' => 'Inserer votre image'
                ]
             ])
            ->add('createdBy')
            ->add('createdAt')
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description du lieu et de ses environs (activités, visites, points positifs comme négatifs'
                ]
            ])
            ->add('image', TextType::class, [
                'attr' => [
                    'placeholder' => 'Inserer votre image'
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
