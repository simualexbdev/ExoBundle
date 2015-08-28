<?php

/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 29/06/15
 * Time: 16:05
 */

namespace UJM\ExoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * InteractionTimedQcm
 *
 * @ORM\Table(name="ujm_interaction_timedQcm")
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Repository\InteractionTimedQcmRepository")
 */
class InteractionTimedQcm
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
     * Le temps est-il limité pour répondre à la question ?
     *
     * @var boolean
     *
     * @ORM\Column(name="limited_time", type="boolean", nullable=true)
     */
    private $limitedTime;

    /**
     * Durée du temps imparti pour répondre à la question.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="duration", type="time", nullable=true)
     */
    private $duration;

    /**
     * Les choix de la question sont-ils ordonnés de manière aléatoire ?
     *
     * @var boolean
     *
     * @ORM\Column(name="shuffle", type="boolean", nullable=true)
     */
    private $shuffle;

    /**
     * Score pour une bonne réponse en cas d'absence de pondération des choix de manière individuelle.
     *
     * @var float
     *
     * @ORM\Column(name="score_right_response", type="float", nullable=true)
     */
    private $scoreRightResponse;

    /**
     * Score pour une réponse fausse en cas d'absence de pondération des choix de manière individuelle.
     *
     * @var float
     *
     * @ORM\Column(name="score_false_response", type="float", nullable=true)
     */
    private $scoreFalseResponse;

    /**
     * Le score des choix de la question doit-il être pondéré de manière individuelle ?
     *
     * @var boolean
     *
     * @ORM\Column(name="weight_response", type="boolean", nullable=true)
     */
    private $weightResponse;

    /*
     * Liste des choix possibles (bon et faux) pour cette question.
     */
    /**
     *
     * @ORM\OneToMany(targetEntity="UJM\ExoBundle\Entity\TimedQcmChoice", mappedBy="interactionTimedQcm", cascade={"remove"})
     */
    private $choices;

    /**
     * @ORM\OneToOne(targetEntity="UJM\ExoBundle\Entity\Interaction", cascade={"remove"})
     */
    private $interaction;

    /**
     * @ORM\ManyToOne(targetEntity="UJM\ExoBundle\Entity\TypeTimedQcm")
     * @ORM\JoinColumn(name="type_timedQcm_id", referencedColumnName="id")
     */
    private $typeTimedQcm;

    /**
     * @var text $htmlCourseComplement
     *
     * @ORM\Column(name="html_course_complement", type="text", nullable=true)
     */
    private $htmlCourseComplement;

    /**
     * Durée du temps imparti pour lire le complement de cours avant de pouvoir passer à la question suivante. (A IMPLEMENTER)
     *
     * @var \DateTime
     *
     * @ORM\Column(name="html_course_complement_duration", type="time", nullable=true)
     */
    private $htmlCourseComplementDuration;


    /**
     * Constructs a new instance of choices
     */
    public function __construct()
    {
        $this->choices = new \Doctrine\Common\Collections\ArrayCollection;
        $this->setLimitedTime(true);
        $this->setShuffle(false);
        $this->setWeightResponse(false);
    }

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
     * Set limitedTime
     *
     * @param boolean $limitedTime
     *
     * @return InteractionTimedQcm
     */
    public function setLimitedTime($limitedTime)
    {
        $this->limitedTime = $limitedTime;

        return $this;
    }

    /**
     * Get limitedTime
     *
     * @return boolean
     */
    public function getLimitedTime()
    {
        return $this->limitedTime;
    }

    /**
     * Set duration
     *
     * @param \DateTime $duration
     *
     * @return InteractionTimedQcm
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return \DateTime
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set shuffle
     *
     * @param boolean $shuffle
     *
     * @return InteractionTimedQcm
     */
    public function setShuffle($shuffle)
    {
        $this->shuffle = $shuffle;

        return $this;
    }

    /**
     * Get shuffle
     *
     * @return boolean
     */
    public function getShuffle()
    {
        return $this->shuffle;
    }

    /**
     * Set scoreRightResponse
     *
     * @param float $scoreRightResponse
     *
     * @return InteractionTimedQcm
     */
    public function setScoreRightResponse($scoreRightResponse)
    {
        $this->scoreRightResponse = $scoreRightResponse;

        return $this;
    }

    /**
     * Get scoreRightResponse
     *
     * @return float
     */
    public function getScoreRightResponse()
    {
        return $this->scoreRightResponse;
    }

    /**
     * Set scoreFalseResponse
     *
     * @param float $scoreFalseResponse
     *
     * @return InteractionTimedQcm
     */
    public function setScoreFalseResponse($scoreFalseResponse)
    {
        $this->scoreFalseResponse = $scoreFalseResponse;

        return $this;
    }

    /**
     * Get scoreFalseResponse
     *
     * @return float
     */
    public function getScoreFalseResponse()
    {
        return $this->scoreFalseResponse;
    }

    /**
     * Set weightResponse
     *
     * @param boolean $weightResponse
     *
     * @return InteractionTimedQcm
     */
    public function setWeightResponse($weightResponse)
    {
        $this->weightResponse = $weightResponse;

        return $this;
    }

    /**
     * Get weightResponse
     *
     * @return boolean
     */
    public function getWeightResponse()
    {
        return $this->weightResponse;
    }

    public function getInteraction()
    {
        return $this->interaction;
    }

    public function setInteraction(\UJM\ExoBundle\Entity\Interaction $interaction)
    {
        $this->interaction = $interaction;
    }

    public function getTypeTimedQcm()
    {
        return $this->typeTimedQcm;
    }

    public function setTypeTimedQcm(\UJM\ExoBundle\Entity\TypeTimedQcm $typeTimedQcm)
    {
        $this->typeTimedQcm = $typeTimedQcm;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function addChoice(\UJM\ExoBundle\Entity\TimedQcmChoice $choice)
    {
        $this->choices[] = $choice;
        //le choix est bien lié à l'entité InteractionTimedQcm, mais dans l'entité timedQcmchoice il faut
        //aussi lié l'InteractionTimedQcm double travail avec les relations bidirectionnelles avec
        //lesquelles il faut bien faire attention à garder les données cohérentes dans un autre
        //script il faudra exécuter $interactionTimedQcm->addChoice() qui garde la cohérence entre les
        //deux entités, il ne faudra pas exécuter $choice->setInteractionTimedQcm(), car lui ne garde
        //pas la cohérence.
        $choice->setInteractionTimedQcm($this);
    }

    /**
     * Set htmlCourseComplement
     *
     * @param text $htmlCourseComplement
     */
    public function setHtmlCourseComplement($htmlCourseComplement)
    {
        $this->htmlCourseComplement = $htmlCourseComplement;
    }

    /**
     * Get htmlCourseComplement
     *
     * @return text
     */
    public function getHtmlCourseComplement()
    {
        return $this->htmlCourseComplement;
    }

    /**
     * Set htmlCourseComplementDuration
     *
     * @param \DateTime $htmlCourseComplementDuration
     *
     * @return InteractionTimedQcm
     */
    public function setHtmlCourseComplementDuration($htmlCourseComplementDuration)
    {
        $this->htmlCourseComplementDuration = $htmlCourseComplementDuration;

        return $this;
    }

    /**
     * Get htmlCourseComplementDuration
     *
     * @return \DateTime
     */
    public function getHtmlCourseComplementDuration()
    {
        return $this->htmlCourseComplementDuration;
    }


    public function shuffleChoices()
    {
        $this->sortChoices();
        $i = 0;
        $tabShuffle = array();
        $tabFixed   = array();
        $choices = new \Doctrine\Common\Collections\ArrayCollection;
        $choiceCount = count($this->choices);
        while ($i < $choiceCount) {
            if ($this->choices[$i]->getPositionForce() === false) {
                $tabShuffle[$i] = $i;
                $tabFixed[] = -1;
            } else {
                $tabFixed[] = $i;
            }

            $i++;
        }
        shuffle($tabShuffle);

        $i = 0;
        $choiceCount = count($this->choices);

        while ($i < $choiceCount) {
            if ($tabFixed[$i] != -1) {
                $choices[] = $this->choices[$i];
            } else {
                $index = $tabShuffle[0];
                $choices[] = $this->choices[$index];
                unset($tabShuffle[0]);
                $tabShuffle = array_merge($tabShuffle);
            }

            $i++;
        }

        $this->choices = $choices;
    }

    public function sortChoices()
    {
        $tab = array();
        $choices = new \Doctrine\Common\Collections\ArrayCollection;

        foreach ($this->choices as $choice) {

            $tab[] = $choice->getOrdre();
        }

        asort($tab);

        foreach (array_keys($tab) as $indice) {
            $choices[] = $this->choices[$indice];
        }

        $this->choices = $choices;
    }

    public function __clone() {
        if ($this->id) {
            $this->id = null;

            $this->interaction = clone $this->interaction;

            $newChoices = new \Doctrine\Common\Collections\ArrayCollection;
            foreach ($this->choices as $choice) {
                $newChoice = clone $choice;
                $newChoice->setInteractionTimedQcm($this);
                $newChoices->add($newChoice);
            }
            $this->choices = $newChoices;

        }
    }
}