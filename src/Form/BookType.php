<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cover', UrlType::class, [
                'label' => 'Couverture du livre',
                'attr' => ['placeholder' => 'Télécharger la couverture du livre']
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre du livre',
                'attr' => ['placeholder' => 'Taper le titre du livre']
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'attr' => ['placeholder' => 'Taper l\'auteur du livre']
            ])
            ->add('editor', TextType::class, [
                'label' => 'Editeur',
                'attr' => ['placeholder' => 'Taper l\'éditeur du livre']
            ])
            ->add('isbn', NumberType::class, [
                'label' => 'Isbn',
                'attr' => ['placeholder' => 'Taper l\'ISBN du livre']
            ])
            ->add('issn', NumberType::class, [
                'label' => 'Issn',
                'attr' => ['placeholder' => 'Taper l\'ISSN du livre']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
