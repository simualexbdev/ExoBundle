<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 07/07/15
 * Time: 14:38
 */

namespace UJM\ExoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimedQcmChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ordre', 'text'
            )
            ->add(
                'rightResponse', 'checkbox', array(
                    'required' => false, 'label' => ' '
                )
            )
            ->add(
                'label', 'textarea', array(
                    'label' => ' ',
                    'required' => true,
                    'attr' => array('style' => 'height:34px; ',
                        'class' => 'form-control',
                        'placeholder' => 'choice'
                    )
                )
            )
            ->add(
                'weight', 'text', array(
                    'required' => false,
                    'label' => ' ',
                    'attr' => array('class' => 'col-md-1', 'placeholder' => 'Choice.weight')
                )
            )
            ->add(
                'feedback', 'textarea', array(
                    'required' => false, 'label' => ' ',
                    'attr' => array('class' => 'form-control',
                        'data-new-tab' => 'yes',
                        'placeholder' => 'Choice.feedback',
                        'style' => 'height:34px;'
                    )
                )
            )
            ->add(
                'positionForce', 'checkbox', array(
                    'required' => false, 'label' => ' '
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'UJM\ExoBundle\Entity\TimedQcmChoice',
            )
        );
    }

    public function getName()
    {
        return 'ujm_exobundle_timedQcmchoicetype';
    }
}