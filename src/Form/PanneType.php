<?php

namespace App\Form;

use App\Entity\Machine;
use App\Entity\Panne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PanneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePanne', null, [
                'widget' => 'single_text',
                'label' => 'Date de la panne',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('severite', ChoiceType::class, [
                'label' => 'Sévérité',
                'choices' => [
                    'Mineure' => 'mineure',
                    'Modérée' => 'moderee',
                    'Critique' => 'critique',
                ],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en_attente',
                    'En cours' => 'en_cours',
                    'Résolue' => 'resolue',
                ],
            ])
            ->add('actionsNecessaires', TextareaType::class, [
                'label' => 'Actions nécessaires',
                'required' => false,
            ])
            ->add('dateResolution', null, [
                'widget' => 'single_text',
                'label' => 'Date de résolution',
                'required' => false,
            ])
            ->add('machine', EntityType::class, [
                'class' => Machine::class,
                'choice_label' => 'nom',
                'label' => 'Machine concernée',
                'placeholder' => 'Choisissez une machine',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panne::class,
        ]);
    }
}
