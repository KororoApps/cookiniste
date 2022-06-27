<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cover', FileType::class, [
                'label' => 'Couverture du livre',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        // 'mimeTypes' => [
                        //     'image/jpeg',
                        //     'image/jpg',
                        //     'image/png',
                        // ],
                        // 'mimeTypesMessage' => 'Merci d\'uploader une couverture'
                    ])
                ],
                'attr' => ['placeholder' => 'Télécharger la couverture du livre']
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre du livre',
                'attr' => ['placeholder' => 'Taper le titre du livre'],
                'required' => false
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'attr' => ['placeholder' => 'Taper l\'auteur du livre'],
                'required' => false
            ])
            ->add('editor', TextType::class, [
                'label' => 'Editeur',
                'attr' => ['placeholder' => 'Taper l\'éditeur du livre'],
                'required' => false
            ])
            ->add('isbn', TextType::class, [
                'label' => 'Isbn',
                'attr' => ['placeholder' => 'Taper l\'ISBN du livre'],
                'required' => false
            ])
            ->add('issn', TextType::class, [
                'label' => 'Issn',
                'attr' => ['placeholder' => 'Taper l\'ISSN du livre'],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
