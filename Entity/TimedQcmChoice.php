<?php

/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 07/07/15
 * Time: 12:10
 */

namespace UJM\ExoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UJM\ExoBundle\Entity\Choice;

/**
 * TimedQcmChoice
 *
 * @ORM\Table(name="ujm_timedQcm_choice")
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Repository\TimedQcmChoiceRepository")
 */
class TimedQcmChoice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $label
     *
     * @ORM\Column(name="label", type="text")
     */
    private $label;

    /**
     * @var integer $ordre
     *
     * @ORM\Column(name="ordre", type="integer")
     */
    private $ordre;

    /**
     * @var integer $weight
     *
     * @ORM\Column(name="weight", type="float", nullable=true)
     */
    private $weight;

    /**
     * @var text $feedback
     *
     * @ORM\Column(name="feedback", type="text", nullable=true)
     */
    private $feedback;

    /**
     * @var boolean $rightResponse
     *
     * @ORM\Column(name="right_response", type="boolean", nullable=true)
     */
    private $rightResponse;

    /**
     * @var boolean $positionForce
     *
     * @ORM\Column(name="position_force", type="boolean", nullable=true)
     */
    private $positionForce;

    /**
     * @ORM\ManyToOne(targetEntity="UJM\ExoBundle\Entity\InteractionTimedQcm", inversedBy="choices")
     * @ORM\JoinColumn(name="interaction_timedQcm_id", referencedColumnName="id")
     */
    private $interactionTimedQcm;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set feedback
     *
     * @param text $feedback
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Get feedback
     *
     * @return text
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set rightResponse
     *
     * @param boolean $rightResponse
     */
    public function setRightResponse($rightResponse)
    {
        $this->rightResponse = $rightResponse;
    }

    /**
     * Get rightResponse
     */
    public function getRightResponse()
    {
        return $this->rightResponse;
    }

    /**
     * Set positionForce
     *
     * @param boolean $positionForce
     */
    public function setPositionForce($positionForce)
    {
        $this->positionForce = $positionForce;
    }

    /**
     * Get positionForce
     */
    public function getPositionForce()
    {
        return $this->positionForce;
    }

    /**
     * Get interactionTimedQcm
     */
    public function getInteractionTimedQcm()
    {
        return $this->interactionTimedQcm;
    }

    /**
     * Set interactionTimedQcm
     *
     * @param InteractionTimedQcm $interactionTimedQcm
     */
    public function setInteractionTimedQcm(\UJM\ExoBundle\Entity\InteractionTimedQcm $interactionTimedQcm)
    {
        $this->interactionTimedQcm = $interactionTimedQcm;
    }
}

