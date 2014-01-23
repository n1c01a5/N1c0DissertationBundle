<?php

namespace N1c0\DissertationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DissertationType extends AbstractType
{
    private $commentClass;

    public function __construct($commentClass)
    {
        $this->commentClass = $commentClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => $this->commentClass,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'n1c0_dissertation_dissertation';
    }
}
