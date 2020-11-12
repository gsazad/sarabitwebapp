<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name')
                ->add('title')
                ->add('keywords', TextType::class)
                ->add('description', TextType::class)
                ->add('body', TextareaType::class, array('attr' => array('novalidate' => 'novalidate')))
                ->add('menu', EntityType::class, array(
                    'class' => 'App:Menu',
                    'choice_label' => function($c) {
                        if ($c->getParent()) {
                            return $c->getName() . " -- " . $c->getParent()->getName();
                        } else {
                            return $c->getName();
                        }
                    },
                ))
                ->add('url', TextType::class, array('required' => false))
                ->add('target', ChoiceType::class, array('choices' => array('Open in Same Window' => '_top', 'Open In New Window' => '_blank')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Page'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_page';
    }

}
