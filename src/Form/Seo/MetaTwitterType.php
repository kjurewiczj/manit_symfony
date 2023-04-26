<?php

namespace App\Form\Seo;

use App\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaTwitterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('twitterTitle', TextType::class, [
                'label' => 'Meta tytuł Twitter'
            ])
            ->add('twitterDescription', TextareaType::class, [
                'label' => 'Meta opis Twitter'
            ])
            ->add('twitterImage', FileType::class, [
                'label' => 'Zdjęcie Twitter',
                'data_class' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
        ]);
    }
}
