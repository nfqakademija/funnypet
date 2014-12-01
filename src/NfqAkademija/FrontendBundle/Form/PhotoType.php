<?php

namespace NfqAkademija\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
                ->add('tags', 'collection', array(
                    'type' => new TagType(),
                    'allow_add'    => true,
                ))
                ->add('fileName', 'file')
                ->add('upload', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NfqAkademija\FrontendBundle\Entity\Photo'
        ));
    }

    public function getName()
    {
        return 'photo';
    }
}