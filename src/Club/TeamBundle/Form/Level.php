<?php

namespace Club\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Level extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('level_name', 'text', array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label_attr' => array(
                    'class' => 'col-sm-2'
                )
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Club\TeamBundle\Entity\Level'
        ));
    }

    public function getName()
    {
        return 'level';
    }
}
