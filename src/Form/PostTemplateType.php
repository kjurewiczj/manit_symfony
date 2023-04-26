<?php

namespace App\Form;

use App\Entity\PostTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', CheckboxType::class, [
                'label' => 'Czy post ma posiadać tytuł?',
                'required' => false
            ])
            ->add('description', CheckboxType::class, [
                'label' => 'Czy post ma posiadać opis?',
                'required' => false
            ])
            ->add('price', CheckboxType::class, [
                'label' => 'Czy post ma posiadać cenę?',
                'required' => false
            ])
            ->add('image', CheckboxType::class, [
                'label' => 'Czy post ma posiadać zdjęcie?',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostTemplate::class,
        ]);
    }
}
