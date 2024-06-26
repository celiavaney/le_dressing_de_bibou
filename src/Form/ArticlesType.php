<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Enfants;
use App\Entity\Tailles;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\CategoriesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('photo', FileType::class, [
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new File([
                        "mimeTypes" => [ "image/jpeg", "image/gif", "image/png", "image/heic" ],
                        "mimeTypesMessage" => "Formats acceptés : gif, jpg, png, heic",
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "Taille maximale du fichier : 2 Mo"
                    ])
                ],
                "help" => "Formats autorisés : images jpg, png, heic ou gif"
            ])
            ->add('sexe', ChoiceType::class,[
                'multiple' => false,
                "required" => true,
                'expanded' => true,
                'choices'  => [
                    'fille' => 'fille',
                    'garçon' => 'garçon',
                    'unisexe' => 'unisexe',
                ],
            ])
            ->add('description')
            ->add('prixAchete', MoneyType::class, [
                'currency' => 'EUR',
                'mapped' => false,])
            // ->add('prixVente')
            ->add('offertPar')
            ->add('enfants', EntityType::class, [
                'class' => Enfants::class,
                'choice_label' => 'prenom',
                'multiple' => false,
                'expanded' => true,
                "required" => true,
            ])
            ->add('user', EntityType::class, [
                "class" => User::class, 
                "choice_label" => "email",
                "placeholder" => "Sélectionner un client *",
                // 'class' => User::class,
                // 'choice_label' => 'email',
                // 'multiple' => false,
                // 'expanded' => true,
                // "required" => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => true
            ])
            ->add('tailles', EntityType::class, [
                'class' => Tailles::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => true,
                "required" => true
            ])
            ->add('quantity', IntegerType::class, [
                'constraints' => [
                    new Range([
                        "min" => 1,
                        "minMessage" => "La quantité doit être supérieure ou égale à 1"
                    ])
                ]
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
