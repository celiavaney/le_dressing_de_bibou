<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Enfants;
use App\Entity\Tailles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('photo')
            ->add('sexe')
            ->add('description')
            ->add('prixAchete')
            ->add('prixVente')
            ->add('offertPar')
            ->add('enfants', EntityType::class, [
                'class' => Enfants::class,
'choice_label' => 'id',
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
'choice_label' => 'id',
            ])
            ->add('tailles', EntityType::class, [
                'class' => Tailles::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
