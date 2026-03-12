<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'label'   => 'Note',
                'choices' => [
                    '⭐ 1' => 1,
                    '⭐⭐ 2' => 2,
                    '⭐⭐⭐ 3' => 3,
                    '⭐⭐⭐⭐ 4' => 4,
                    '⭐⭐⭐⭐⭐ 5' => 5,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr'  => [
                    'placeholder' => 'Partagez votre avis...',
                    'rows'        => 4,
                ],
                'constraints' => [
                    new NotBlank(message: 'Le commentaire ne peut pas être vide.'),
                    new Length(min: 10, minMessage: 'Minimum 10 caractères.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}