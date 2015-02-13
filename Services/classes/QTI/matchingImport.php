<?php

/**
* To import a Matching question in QTI
*
*/

namespace UJM\ExoBundle\Services\classes\QTI;

use UJM\ExoBundle\Entity\Proposal;
use UJM\ExoBundle\Entity\Label;
use UJM\ExoBundle\Entity\InteractionMatching;

class matchingImport extends qtiImport {

    protected $interactionMatching;
    protected $associatedLabels = [];

    /**
    * Implements the abstract method
    *
    * @access public
    *
    * @param qtiRepository $qtiRepos
    * @param DOMElement $assessmentItem assessmentItem of the question to imported
    */
    public function import(qtiRepository $qtiRepos, $assessmentItem) {
        $this->qtiRepos = $qtiRepos;
        $this->getQTICategory();
        $this->initAssessmentItem($assessmentItem);
        $this->createQuestion();
        $this->createInteraction();
        $this->interaction->setType('InteractionMatching');
        $this->doctrine->getManager()->persist($this->interaction);
        $this->doctrine->getManager()->flush();
        $this->createInteractionMatching();

        return $this->interactionMatching;
    }

    /**
    * Implements the abstract method
    *
    * @access protected
    *
    * @return $text
    */
    protected function getPrompt() {
        $prompt = '';
        $ib = $this->assessmentItem->getElementsByTagName("itemBody")->item(0);
        $ci = $ib->getElementsByTagName("matchInteraction")->item(0);
        $text = '';
        if ($ci->getElementsByTagName("prompt")->item(0)) {
            //$prompt = $ci->getElementsByTagName("prompt")->item(0)->nodeValue;
            $prompt = $ci->getElementsByTagName("prompt")->item(0);
            $text = $this->domElementToString($prompt);
            $text = str_replace('<prompt>', '', $text);
            $text = str_replace('</prompt>', '', $text);
            $text = html_entity_decode($text);
        }
        return $text;
    }

    /**
    * Create the InteractionMatching object
    *
    * @access protected
    */
    protected function createInteractionMatching() {
        $rp = $this->assessmentItem->getElementsByTagName("responseProcessing");
        $this->interactionMatching = new InteractionMatching();
        $this->interactionMatching->setInteraction($this->interaction);
        //for recording the type of the question
        $this->matchingType();
        $this->doctrine->getManager()->persist($this->interactionMatching);
        $this->doctrine->getManager()->flush();
        $this->createLabels();
        $this->createProposals();
    }

    /**
     * Create Labels in BDD
     *
     * @access protected
     */
    protected function createLabels() {
        $ib = $this->assessmentItem->getElementsByTagName("itemBody")->item(0);
        $mi = $ib->getElementsByTagName("matchInteraction")->item(0);
        $sms = $mi->getElementsByTagName("simpleMatchSet")->item(1);

        foreach ($sms->getElementsByTagName("simpleAssociableChoice") as $simpleLabel) {
            //create a new Label and set attributes
            $label = new Label();
            $label->setValue($this->value($simpleLabel));
            $identifiant = $simpleLabel->getAttribute("identifier");
            $label->setScoreRightResponse($this->notation($identifiant));
            $label->setInteractionMatching($this->interactionMatching);
            //recording in the DBB
            $this->doctrine->getManager()->persist($label);
            $this->doctrine->getManager()->flush();
            $this->associatedLabels[$identifiant] = $label;
        }
    }

    /**
     * Create Proposals in BDD
     *
     * @access protected
     */
    protected function createProposals() {
        $ib = $this->assessmentItem->getElementsByTagName("itemBody")->item(0);
        $mi = $ib->getElementsByTagName("matchInteraction")->item(0);
        $sms = $mi->getElementsByTagName("simpleMatchSet")->item(0);
        $labels = $this->associatedLabels;
        $allRelations = $this->relations();

        // foreach proposal into the export file
        foreach ($sms->getElementsByTagName("simpleAssociableChoice") as $simpleProposal) {
            $proposal = new Proposal();
            $proposal->setValue($this->value($simpleProposal));
            $identifiant = $simpleProposal->getAttribute("identifier");
            $proposal->setInteractionMatching($this->interactionMatching);
            $this->doctrine->getManager()->persist($proposal);
            $this->doctrine->getManager()->flush();
            $rightLabel = 0;
            //compare all relations to the proposal selected
            foreach ($allRelations as $relation) {
                if (stripos($relation, $identifiant) !== false) {
                    $rightLabel = $relation;
                    $rightLabel = str_replace($identifiant, '', $rightLabel);
                    $rightLabel = str_replace(' ', '', $rightLabel);
                }
            }
            // foreach label of the export file, compare to the right relation
            foreach ($labels as $key => $label) {
                if ($key == $rightLabel) {
                    $proposal->addAssociatedLabel($label);
                    $proposal->setInteractionMatching($this->interactionMatching);
                    $this->doctrine->getManager()->persist($proposal);
                    $this->doctrine->getManager()->flush();
                }
            }
        }
    }

    /**
     * get all relations of the question
     *
     * @access protected
     *
     * @return $allRelations
     */
    protected function relations() {
        $rd = $this->assessmentItem->getElementsByTagName("responseDeclaration")->item(0);
        $cr = $rd->getElementsByTagName("mapping")->item(0);
        $allRelations = [];

        foreach ($cr->getElementsByTagName("mapEntry") as $key => $relation) {
            $allRelations[$key] = $relation->getAttribute("mapKey");
        }
        return $allRelations;
    }

    /**
     * Get value of the balise
     *
     * @access protected
     *
     * @param type $balise
     *
     * @return $value
     */
    protected function value($balise) {
        $value = $this->domElementToString($balise);
        $value = preg_replace('(<simpleAssociableChoice.*?>)', '', $value);
        $value = str_replace('</simpleAssociableChoice>', '', $value);
        $value = html_entity_decode($value);

        return $value;
    }

    /**
     * Get the score of the relation
     *
     * @access protected
     *
     * @param type $identifiant
     *
     * @return $notation
     */
    protected function notation($identifiant) {
        $m = $this->assessmentItem->getElementsByTagName("mapping")->item(0);
        $notation = 0;
        foreach($m->getElementsByTagName("mapEntry") as $relation) {
            $value = $relation->getAttribute("mapKey");
            //if mapEntry match to identifier add the notation
            if (stripos($value, $identifiant)) {
                $notation = $notation + $relation->getAttribute("mappedValue");
            }
        }
        return $notation;
    }

    /**
    * Get Type Matching
    *
    * @access protected
    */
    protected function matchingType() {
        $ri = $this->assessmentItem->getElementsByTagName("responseDeclaration")->item(0);
        if ($ri->hasAttribute("cardinality") && $ri->getAttribute("cardinality") == 'single') {
            //type : to drag
            $type = $this->doctrine
                ->getManager()
                ->getRepository('UJMExoBundle:TypeMatching')
                ->findOneBy(array('code' => 2));
            $this->interactionMatching->setTypeMatching($type);
        } else {
            //type : to bind
            $type = $this->doctrine
                ->getManager()
                ->getRepository('UJMExoBundle:TypeMatching')
                ->findOneBy(array('code' => 1));
            $this->interactionMatching->setTypeMatching($type);
        }
    }
}