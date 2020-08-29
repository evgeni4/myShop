<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
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
            ->add('newPrice')
            ->add('discount')
            ->add('discountStart',TextType::class,['empty_data' => ''])
            ->add('discountEnd',TextType::class,['empty_data' => ''])
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
