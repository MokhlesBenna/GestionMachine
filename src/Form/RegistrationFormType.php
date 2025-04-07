<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user_type', ChoiceType::class, [
                'label' => 'Register As',
                'choices' => [
                    'Admin' => 'admin',
                    'Technicien' => 'technicien',
                ],
                'required' => true,
                'placeholder' => 'Choose your role',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
            ])
            // Extra fields for Technicien
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'required' => false,
                'label' => 'Prénom',
            ])
            ->add('specialite', TextType::class, [
                'required' => false,
                'label' => 'Spécialité',
            ])
            ->add('telephone', TextType::class, [
                'required' => false,
                'label' => 'Téléphone',
            ])
            ->add('localisation', TextType::class, [
                'required' => false,
                'label' => 'Localisation',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
