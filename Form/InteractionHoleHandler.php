<?php

namespace UJM\ExoBundle\Form;

use UJM\ExoBundle\Entity\InteractionHole;
use UJM\ExoBundle\Entity\WordResponse;

class InteractionHoleHandler extends \UJM\ExoBundle\Form\InteractionHandler{

    protected $validator;

    public function setValidator($validator) {
        $this->validator = $validator;
    }

    public function processAdd()
    {
        if ( $this->request->getMethod() == 'POST' ) {
            $this->form->handleRequest($this->request);

            if ( $this->form->isValid() ) {

                if($this->validateNbClone() === FALSE) {
                    return 'infoDuplicateQuestion';
                }

                foreach ($this->form->getData()->getHoles() as $h) {
                    foreach ($h->getWordResponses() as $wr) {
                        $errorList = $this->validator->validate($wr);
                        if (count($errorList) > 0) {
                            //echo 'test : '.$errorList[0]->getMessage();die();
                            return $errorList[0]->getMessage();
                        }
                    }
                }
                $this->onSuccessAdd($this->form->getData());

                return true;
            }
        }

        return false;
    }

    protected function onSuccessAdd($interHole)
    {
        // to avoid bug with code tinymce
        $htmlTiny = $interHole->getHtml();
        $interHole->getInteraction()->getQuestion()->setDateCreate(new \Datetime());
        $interHole->getInteraction()->getQuestion()->setUser($this->user);
        $interHole->getInteraction()->setType('InteractionHole');

        foreach ($interHole->getHoles() as $hole) {
            foreach ($hole->getWordResponses() as $wr) {
                //$hole->addWordResponse($wr);
                $wr->setHole($hole);
                $this->em->persist($wr);
            }
            //$interHole->addHole($hole);
            $hole->setInteractionHole($interHole);
            $this->em->persist($hole);
        }
        $interHole->setHtml($htmlTiny);
        $this->em->persist($interHole);
        $this->em->persist($interHole->getInteraction()->getQuestion());
        $this->em->persist($interHole->getInteraction());

        $this->persistHints($interHole);

        $this->em->flush();

        $this->htmlWithoutValue($interHole);

        $this->addAnExericse($interHole);

        $this->duplicateInter($interHole);
    }

    public function processUpdate($originalInterHole)
    {
        $originalHoles = array();
        $originalHints = array();

        // Create an array of the current Choice objects in the database
        foreach ($originalInterHole->getHoles() as $hole) {
            $originalHoles[] = $hole;
        }
        foreach ($originalInterHole->getInteraction()->getHints() as $hint) {
            $originalHints[] = $hint;
        }

        if ( $this->request->getMethod() == 'POST' ) {
            $this->form->handleRequest($this->request);

            if ( $this->form->isValid() ) {
                foreach ($this->form->getData()->getHoles() as $h) {
                    foreach ($h->getWordResponses() as $wr) {
                        $errorList = $this->validator->validate($wr);
                        if (count($errorList) > 0) {
                            //echo 'test : '.$errorList[0]->getMessage();die();
                            return $errorList[0]->getMessage();
                        }
                    }
                }
                $this->onSuccessUpdate($this->form->getData(), $originalHoles, $originalHints);

                return true;
            }
        }

        return false;
    }

    protected function onSuccessUpdate()
    {
        $arg_list = func_get_args();
        $interHole = $arg_list[0];
        $originalHoles = $arg_list[1];
        $originalHints = $arg_list[2];

        // to avoid bug with code tinymce
        $htmlTiny = $interHole->getHtml();

        // filter $originalHoles to contain hole no longer present
        foreach ($interHole->getHoles() as $hole) {

            //to remove key word not yet used
            $this->delKeyWord($hole, $originalHoles);

            foreach ($originalHoles as $key => $toDel) {
                if ($toDel->getId() == $hole->getId()) {
                    unset($originalHoles[$key]);
                }
            }
        }

        // remove the relationship between the hole and the interactionhole
        foreach ($originalHoles as $hole) {
            // remove the hole from the interactionhole
            $interHole->getHoles()->removeElement($hole);

            // if you wanted to delete the Hole entirely, you can also do that
            $this->em->remove($hole);
        }

        $this->modifyHints($interHole, $originalHints);

        $interHole->setHtml($htmlTiny);
        $this->em->persist($interHole);
        $this->em->persist($interHole->getInteraction()->getQuestion());
        $this->em->persist($interHole->getInteraction());

        // On persiste tous les holes de l'interaction hole.
        foreach ($interHole->getHoles() as $hole) {
            foreach ($hole->getWordResponses() as $wr) {
                //$hole->addWordResponse($wr);
                $wr->setHole($hole);
                $this->em->persist($wr);
            }
            $interHole->addHole($hole);
            $this->em->persist($hole);
        }

        $this->em->flush();

        $this->htmlWithoutValue($interHole);
    }

    private function delKeyWord($hole, $originalHoles)
    {
        $wordResponses = $hole->getWordResponses()->toArray();

        foreach($originalHoles as $holeOrig)
        {
            $originalWords = $holeOrig->getwordResponses()->getSnapshot();
            if($hole->getId() === $holeOrig->getId())
            {
                foreach($wordResponses as $word)
                {
                    foreach($originalWords as $key => $toDel)
                    {
                        if ($toDel->getId() === $word->getId())
                        {
                            unset($originalWords[$key]);
                        }
                    }
                }

                // remove the relationship between the hole and the interactionhole
                foreach ($originalWords as $word)
                {
                    // remove the wr from the wordResponse
                    $hole->getWordResponses()->removeElement($word);

                    // if you wanted to delete the Hole entirely, you can also do that
                    $this->em->remove($word);
                }

            }
        }
    }

    private function htmlWithoutValue($interHole)
    {
        //id hole in html = $hole->getPosition()
        $html = $interHole->getHtml();
        $tabInputValue = explode('value="', $html);
        $tabHoles = array();

        foreach($interHole->getHoles() as $hole)
        {
            if ($hole->getSelector() === false) {
                $tabHoles[$hole->getPosition()] = $hole;
            } else {
                $selectInHtml = explode('<select id="' . $hole->getPosition() . '" class="blank" name="blank_' . $hole->getPosition() . '">', $html);
                $selectInHtml = explode ('</select>', $selectInHtml[1]);
                $html = str_replace($selectInHtml[0], '', $html);

                $pos = $hole->getPosition();
                $regExpr = '<select id="'.$pos.'" class="blank" name="blank_'.$hole->getPosition().'">';
                $select = '<select id="'.$pos.'" class="blank" name="blank_'.$hole->getPosition().'">';

                $wrs = array();
                foreach ($hole->getWordResponses() as $wr) {
                    $wrs[] = $wr;
                }
                shuffle($wrs);

                foreach ($wrs as $wr) {
                    $id = $wr->getId();
                    $response = $wr->getResponse();
                    $select .= "<option value=\"$id\">$response</option>";
                }
                $select .= '</select>';
                $html = str_replace($regExpr, $select, $html);
            }
        }
        ksort($tabHoles);
        $tabHoles = array_values($tabHoles);

        for( $i= 0; $i < count($tabInputValue); $i++)
        {
            $inputValue = explode('"', $tabInputValue[$i]);
            $regExpr = 'value="'.$inputValue[0].'"';
            $html = str_replace($regExpr, 'value=""', $html);
        }
        $interHole->setHtmlWithoutValue($html);
        $this->em->persist($interHole);
        $this->em->flush();

    }
}

?>
