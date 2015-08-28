<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 02/07/15
 * Time: 14:56
 */

namespace UJM\ExoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeTimedQcmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'UJM\ExoBundle\Entity\TypeTimedQcm',
            )
        );
    }

    public function getName()
    {
        return 'ujm_exobundle_typetimedQcmtype';
    }
}