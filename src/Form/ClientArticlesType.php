<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Enfants;
use App\Entity\Tailles;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\CategoriesType;
use Symfony\Component\Form\AbstractType;
use App\Validator\Constraints\SingleChoice;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ClientArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseigner un nom.',
                    ]),
                ],
            ] )
            ->add('photo', FileType::class, [
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new File([
                        "mimeTypes" => [ "image/jpeg", "image/gif", "image/png" ],
                        "mimeTypesMessage" => "Formats acceptés : gif, jpg, png",
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "Taille maximale du fichier : 2 Mo"
                    ]),
                ],
                "help" => "Formats autorisés : images jpg, png ou gif"
            ])
            ->add('sexe', ChoiceType::class,[
                'multiple' => false,
                'expanded' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Selectionner un sexe.',
                    ]),
                ],
                'choices'  => [
                    'fille' => 'fille',
                    'garçon' => 'garçon',
                    'unisexe' => 'unisexe',
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'entity-checkboxes']; 
                },
            ])
            ->add('description')
            ->add('prixAchete', MoneyType::class, [
                'currency' => 'EUR',
                ])
            // ->add('prixVente')
            ->add('offertPar')
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Selectionner une catégorie.',
                    ]),
                ],
                "choices" => $options["categories"],
                'multiple' => false,
                'expanded' => true,  
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'entity-checkboxes']; 
                },
                
            ])
            ->add('tailles', EntityType::class, [
                'class' => Tailles::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Selectionner une taille.',
                    ]),
                ],
                "choices" => $options["tailles"],
                'multiple' => false,
                'expanded' => true,
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'entity-checkboxes']; 
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
            "categories" => [],
            "tailles" => [],
            'attr' => [
                'novalidate' => 'novalidate', // comment me to reactivate the html5 validation!  🚥
            ]
        ]);
    }
}
