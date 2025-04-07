<?php
namespace App\Form;

use App\Entity\Machine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MachineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Machine Name',
                'required' => true,
            ])
            ->add('type', TextType::class, [
                'label' => 'Machine Type',
                'required' => true,
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Location',
                'required' => true,
            ])
            ->add('udi', TextType::class, [
                'label' => 'UDI',
                'required' => true,
            ])
            ->add('dateAchat', DateType::class, [
                'label' => 'Purchase Date',
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('frequenceEntretiens', IntegerType::class, [
                'label' => 'Maintenance Frequency',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Active' => 'active',
                    'Inactive' => 'inactive',
                ],
                'required' => true, // Making it required to pick a status
                'expanded' => false, // To display a select dropdown
                'multiple' => false, // Ensures only one status can be selected
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Machine::class,
        ]);
    }
}
