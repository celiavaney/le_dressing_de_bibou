<?php

namespace App\Form;

use App\Entity\Enfants;
use App\Entity\Tailles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaillesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('enfants', EntityType::class, [
                'class' => Enfants::class,
                'choice_label' => 'prenom',
                'multiple' => true,
                'expanded' => true,
                "required" => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tailles::class,
        ]);
    }
}
