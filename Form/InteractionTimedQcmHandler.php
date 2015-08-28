<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 06/07/15
 * Time: 11:16
 */

namespace UJM\ExoBundle\Form;

use UJM\ExoBundle\Entity\TimedQcmChoice;

class InteractionTimedQcmHandler extends \UJM\ExoBundle\Form\InteractionHandler
{

    /**
     * Implements the abstract method
     *
     * @access public
     *
     */
    public function processAdd()
    {
        if ( $this->request->getMethod() == 'POST' ) {
            $this->form->handleRequest($this->request);
            //Uses the default category if no category selected
            $this->checkCategory();
            $this->checkTitle();
            if($this->validateNbClone() === FALSE) {
                return 'infoDuplicateQuestion';
            }

            if ( $this->form->isValid() ) {
                $this->onSuccessAdd($this->form->getData());

                return true;
            }
        }
        echo "FORM_NOT_VALID";

        return false;
    }

    /**
     * Implements the abstract method
     *
     * @access protected
     *
     * @param \UJM\ExoBundle\Entity\InteractionTimedQcm $interTimedQcm
     */
    protected function onSuccessAdd($interTimedQcm)
    {

        // \ pour instancier un objet du namespace global et non pas de l'actuel
        $interTimedQcm->getInteraction()->getQuestion()->setDateCreate(new \Datetime());
        $interTimedQcm->getInteraction()->getQuestion()->setUser($this->user);
        $interTimedQcm->getInteraction()->setType('InteractionTimedQcm');

        $pointsWrong = str_replace(',', '.', $interTimedQcm->getScoreFalseResponse());
        $pointsRight = str_replace(',', '.', $interTimedQcm->getScoreRightResponse());

        $interTimedQcm->setScoreFalseResponse($pointsWrong);
        $interTimedQcm->setScoreRightResponse($pointsRight);

        $this->em->persist($interTimedQcm);
        $this->em->persist($interTimedQcm->getInteraction()->getQuestion());
        $this->em->persist($interTimedQcm->getInteraction());

        // On persiste tous les choices de l'interaction TimedQcm.
        $ord = 1;
        foreach ($interTimedQcm->getChoices() as $choice) {
            $choice->setOrdre($ord);
            $choice->setInteractionTimedQcm($interTimedQcm);

            $this->em->persist($choice);
            $ord = $ord + 1;
        }

        $this->persistHints($interTimedQcm);

        $this->em->flush();

        $this->addAnExercise($interTimedQcm);

        $this->duplicateInter($interTimedQcm);

    }

    /**
     * Implements the abstract method
     *
     * @access public
     *
     * @param \UJM\ExoBundle\Entity\InteractionTimedQcm $originalInterTimedQcm
     *
     * Return boolean
     */
    public function processUpdate($originalInterTimedQcm)
    {
        $originalChoices = array();
        $originalHints = array();

        // Create an array of the current TimedQcmChoice objects in the database
        foreach ($originalInterTimedQcm->getChoices() as $choice) {
            $originalChoices[] = $choice;
        }
        foreach ($originalInterTimedQcm->getInteraction()->getHints() as $hint) {
            $originalHints[] = $hint;
        }

        if ( $this->request->getMethod() == 'POST' ) {
            $this->form->handleRequest($this->request);

            if ( $this->form->isValid() ) {
                $this->onSuccessUpdate($this->form->getData(), $originalChoices, $originalHints);

                return true;
            }
        }

        return false;
    }

    /**
     * Implements the abstract method
     *
     * @access protected
     *
     */
    protected function onSuccessUpdate()
    {
        $arg_list = func_get_args();
        $interTimedQcm = $arg_list[0];
        $originalChoices = $arg_list[1];
        $originalHints = $arg_list[2];

        // filter $originalChoices to contain choice no longer present
        foreach ($interTimedQcm->getChoices() as $choice) {
            foreach ($originalChoices as $key => $toDel) {
                if ($toDel->getId() == $choice->getId()) {
                    unset($originalChoices[$key]);
                }
            }
        }

        // remove the relationship between the choice and the interactiontimedQcm
        foreach ($originalChoices as $choice) {
            // remove the choice from the interactiontimedQcm
            $interTimedQcm->getChoices()->removeElement($choice);

            // if you wanted to delete the TimedQcmChoice entirely, you can also do that
            $this->em->remove($choice);
        }

        $this->modifyHints($interTimedQcm, $originalHints);

        $pointsWrong = str_replace(',', '.', $interTimedQcm->getScoreFalseResponse());
        $pointsRight = str_replace(',', '.', $interTimedQcm->getScoreRightResponse());

        $interTimedQcm->setScoreFalseResponse($pointsWrong);
        $interTimedQcm->setScoreRightResponse($pointsRight);

        $this->em->persist($interTimedQcm);
        $this->em->persist($interTimedQcm->getInteraction()->getQuestion());
        $this->em->persist($interTimedQcm->getInteraction());

        // On persiste tous les choices de l'interaction TimedQcm.
        foreach ($interTimedQcm->getChoices() as $choice) {
            $interTimedQcm->addChoice($choice);
            $this->em->persist($choice);
        }

        $this->em->flush();

    }
}
