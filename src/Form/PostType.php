<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostTemplate;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $postTemplate = $options['postTemplate'];
        if ($postTemplate->isTitle()) {
            $builder->add('title', TextType::class, [
                'label' => 'Tytuł'
            ]);
        }
        if ($postTemplate->isDescription()) {
            $builder->add('content', CKEditorType::class, [
                'label' => 'Treść',
                'config' => [
                    'uiColor' => '#ffffff',
                    'language' => 'pl',
                    'toolbar' => 'post_toolbar'
                ],
            ]);
        }
        if ($postTemplate->isImage()) {
            $builder->add('image', FileType::class, [
                'label' => 'Zdjęcie',
                'required' => false,
                'data_class' => null,
            ]);
        }
        if ($postTemplate->isPrice()) {
            $builder->add('price', NumberType::class, [
                'label' => 'Cena'
            ]);
        }
        $builder->add('status', CheckboxType::class, [
            'label' => 'Czy aktywny',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'postTemplate' => PostTemplate::class
        ]);
    }
}
