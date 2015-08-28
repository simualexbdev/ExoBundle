<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 29/06/15
 * Time: 14:10
 */

namespace UJM\ExoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Claroline\CoreBundle\Entity\User;

use UJM\ExoBundle\Repository\TypeTimedQcmRepository;

class InteractionTimedQcmType extends AbstractType
{
    private $user;
    private $catID;
    private $docID;

    public function __construct(User $user, $catID = -1, $docID = -1)
    {
        $this->user  = $user;
        $this->catID = $catID;
        $this->docID = $docID;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $this->user->getId();

        $builder
            ->add(
                'interaction', new InteractionType(
                    $this->user, $this->catID
                )
            );
        $builder
            ->add(
                'shuffle', 'checkbox', array(
                    'label' => 'qcm_shuffle',
                    'required' => false
                )
            );
        $builder
            ->add(
                'scoreRightResponse', 'text', array(
                    'required' => false,
                    'label' => 'score_right_label',
                    'attr'  => array( 'placeholder' => 'points')
                )
            );
        $builder
            ->add(
                'scoreFalseResponse', 'text', array(
                    'required' => false,
                    'label' => 'score_false_label',
                    'attr'  => array( 'placeholder' => 'points','class'=>'col-md-2'),
                )
            );
        $builder
            ->add(
                'weightResponse', 'checkbox', array(
                    'required' => false,
                    'label' => 'weight_choice'
                )
            );
        $builder
            ->add(
                'typeTimedQcm', 'entity', array(
                    'class' => 'UJM\\ExoBundle\\Entity\\TypeTimedQcm',
                    'label' => 'TypeTimedQcm.value',
                    'data_class' => 'UJM\\ExoBundle\\Entity\\TypeTimedQcm',
                    'multiple' => false,
                    'expanded' => true,
                    'query_builder' => function(TypeTimedQcmRepository $er) {
                        return $er->createQueryBuilder('TypeTimedQcm')
                            ->orderBy('TypeTimedQcm.value', 'DESC');
                    }
                )
            );
        $builder
            ->add(
                'limitedTime', 'checkbox', array(
                    'required' => false,
                    'label' => 'Inter_TimedQcm.limitedTime'
                )
            );
        $builder
            ->add(
                'duration', 'time', array(
                    'required' => false,
                    'label' => 'Inter_TimedQcm.duration',
                    'with_seconds' => true,
                )
            );
        $builder
            ->add(
                'choices', 'collection', array(
                    'type' => new TimedQcmChoiceType,
                    'prototype' => true,
                    'allow_add' => true,
                    'allow_delete' => true
                )
            );
        $builder
            ->add('htmlCourseComplement','tinymce', array(
                    'attr' => array('data-new-tab' => 'yes'),
                    'label' => 'Inter_TimedQcm.html',
                    'attr' => array('data-before-unload' => 'off'),
                    'required' => false
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'UJM\ExoBundle\Entity\InteractionTimedQcm',
                'cascade_validation' => true
            )
        );
    }

    public function getName()
    {
        return 'ujm_exobundle_interactiontimedQcmtype';
    }
}