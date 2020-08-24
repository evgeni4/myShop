<?php

namespace AppBundle\Form;

use AppBundle\Entity\Categories;
use AppBundle\Entity\Metals;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('size')
            ->add('price')
            ->add('discount')
            ->add('description')
            ->add('gender')
            ->add('scaleWeight')
            ->add('image', FileType::class,
                [
                    'multiple' => true,
                    'mapped' => false
                ])
            ->add('category')
            ->add('metalId');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }


}
