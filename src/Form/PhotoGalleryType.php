<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoGalleryType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title')
                ->add('description', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class)
                ->add('fileData', FileType::class, array('label' => 'Select Photo', 'attr' => array('accept' => ".png,.jpg,.jpeg")))
                ->add('album', EntityType::class, array(
                    'class' => 'App:Album',
                    'choice_label' => 'name',
                ));
    }

/**
     * {@inheritdoc}
     */

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PhotoGallery'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_photogallery';
    }

}
