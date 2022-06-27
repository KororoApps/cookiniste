<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Photo de la recette',
                'attr' => ['placeholder' => 'Télécharger la couverture du livre']
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de la recette',
                'attr' => ['placeholder' => 'Titre de la recette'],
                'required' => false
            ])
            ->add('page', NumberType::class, [
                'label' => 'Page',
                'attr' => ['placeholder' => 'Page où la trouver'],
                'required' => false
            ]);

        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
