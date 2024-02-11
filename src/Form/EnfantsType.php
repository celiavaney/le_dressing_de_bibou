<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Enfants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EnfantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('prenom', TextType::class, [
            'label' => 'Prénom *',
            'attr' => ['placeholder' => 'Prénom *'],
            'required' => true,
        ]);

        if ($options['include_user_field']) {
            $builder->add('user', EntityType::class, [
                'class' => User::class, 
                'choice_label' => 'email',
                'placeholder' => 'Sélectionner un client',
                'attr' => [
                    'class' => 'custom-select form-select', 
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enfants::class,
            'include_user_field' => true, // Default value for the new option
        ]);
    }
}
