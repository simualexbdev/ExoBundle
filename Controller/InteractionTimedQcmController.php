<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 29/06/15
 * Time: 15:04
 */

namespace UJM\ExoBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

use UJM\ExoBundle\Entity\Response;
use UJM\ExoBundle\Entity\InteractionTimedQcm;

use UJM\ExoBundle\Form\ResponseType;
use UJM\ExoBundle\Form\InteractionTimedQcmType;
use UJM\ExoBundle\Form\InteractionTimedQcmHandler;

/**
 * InteractionTimedQcm controller.
 *
 */
class InteractionTimedQcmController extends Controller
{

    /**
     * Creates a new InteractionTimedQcm entity.
     *
     * @access public
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $services = $this->container->get('ujm.exercise_services');
        $interTimedQcm = new InteractionTimedQcm();
        $form = $this->createForm(
            new InteractionTimedQcmType(
                $this->container->get('security.token_storage')->getToken()->getUser()
            ), $interTimedQcm
        );

        $exoID = $this->container->get('request')->request->get('exercise');

        //Get the lock category
        $user = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
        $Locker = $this->getDoctrine()->getManager()->getRepository('UJMExoBundle:Category')->getCategoryLocker($user);
        if (empty($Locker)) {
            $catLocker = "";
        } else {
            $catLocker = $Locker[0];
        }

        $exercise = $this->getDoctrine()->getManager()->getRepository('UJMExoBundle:Exercise')->find($exoID);
        $formHandler = new InteractionTimedQcmHandler(
            $form, $this->get('request'), $this->getDoctrine()->getManager(),
            $this->container->get('ujm.exercise_services'),
            $this->container->get('security.token_storage')->getToken()->getUser(), $exercise,
            $this->get('translator')
        );

        $timedQcmHandler = $formHandler->processAdd();
        if ($timedQcmHandler === TRUE) {echo "OK";
            $categoryToFind = $interTimedQcm->getInteraction()->getQuestion()->getCategory();
            $titleToFind = $interTimedQcm->getInteraction()->getQuestion()->getTitle();

            if ($exoID == -1) {
                return $this->redirect(
                    $this->generateUrl('ujm_question_index', array(
                            'categoryToFind' => base64_encode($categoryToFind), 'titleToFind' => base64_encode($titleToFind))
                    )
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('ujm_exercise_questions', array(
                            'id' => $exoID, 'categoryToFind' => $categoryToFind, 'titleToFind' => $titleToFind)
                    )
                );
            }
        }
        if ($timedQcmHandler == 'infoDuplicateQuestion') {
            $form->addError(new FormError(
                $this->get('translator')->trans('infoDuplicateQuestion')
            ));
        }

        $typeTimedQcm = $services->getTypeTimedQcm();
        $formWithError = $this->render(
            'UJMExoBundle:InteractionTimedQcm:new.html.twig', array(
                'entity' => $interTimedQcm,
                'form' => $form->createView(),
                'error' => true,
                'exoID' => $exoID,
                'typeTimedQcm' => json_encode($typeTimedQcm)
            )
        );

        $formWithError = substr($formWithError, strrpos($formWithError, 'GMT') + 3);

        return $this->render(
            'UJMExoBundle:Question:new.html.twig', array(
                'formWithError' => $formWithError,
                'exoID' => $exoID,
                'linkedCategory' => $this->container->get('ujm.exercise_services')->getLinkedCategories(),
                'locker' => $catLocker
            )
        );
    }

    /**
     * Edits an existing InteractionTimedQcm entity.
     *
     * @access public
     *
     * @param integer $id id of InteractionTimedQcm
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($id)
    {
        $exoID = $this->container->get('request')->request->get('exercise');
        $user  = $this->container->get('security.token_storage')->getToken()->getUser();
        $catID = -1;

        $em = $this->getDoctrine()->getManager();

        $interTimedQcm = $em->getRepository('UJMExoBundle:InteractionTimedQcm')->find($id);

        if (!$interTimedQcm) {
            throw $this->createNotFoundException('Unable to find InteractionTimedQcm entity.');
        }

        if ($user->getId() != $interTimedQcm->getInteraction()->getQuestion()->getUser()->getId()) {
            $catID = $interTimedQcm->getInteraction()->getQuestion()->getCategory()->getId();
        }

        $editForm   = $this->createForm(
            new InteractionTimedQcmType(
                $this->container->get('security.token_storage')->getToken()->getUser(),
                $catID
            ), $interTimedQcm
        );
        $formHandler = new InteractionTimedQcmHandler(
            $editForm, $this->get('request'), $this->getDoctrine()->getManager(),
            $this->container->get('ujm.exercise_services'),
            $this->container->get('security.token_storage')->getToken()->getUser(),
            $this->get('translator')
        );

        if ($formHandler->processUpdate($interTimedQcm)) {
            if ($exoID == -1) {

                return $this->redirect($this->generateUrl('ujm_question_index'));
            } else {

                return $this->redirect(
                    $this->generateUrl(
                        'ujm_exercise_questions',
                        array(
                            'id' => $exoID,
                        )
                    )
                );
            }
        }

        return $this->forward(
            'UJMExoBundle:Question:edit', array(
                'exoID' => $exoID,
                'id'    => $interTimedQcm->getInteraction()->getQuestion()->getId(),
                'form'  => $editForm
            )
        );
    }

    /**
     * To test the TimedQcm question by the teacher
     *
     * @access public
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function responseTimedQcmAction()
    {
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $vars = array();
            $exoID = $request->request->get("exoID");
            $interTimedQcmID = $request->request->get("interactionTimedQcmToValidated");
            $interTimedQcm = $em->getRepository('UJMExoBundle:InteractionTimedQcm')
                                 ->findById($interTimedQcmID);

            if ($exoID != -1) {
                $exercise = $em->getRepository('UJMExoBundle:Exercise')->find($exoID);
                $vars['_resource'] = $exercise;
            }

            $exerciseSer = $this->container->get('ujm.exercise_services');
            $res = $exerciseSer->responseTimedQcm($request);

            if ($request->request->get("paperID") != null) {
                $response = '';
                $paper = $em->getRepository('UJMExoBundle:Paper')->findById($request->request->get("paperID"));
                $paper = $paper[0];
                $ip = $exerciseSer->getIP($request);
                $response = $em->getRepository('UJMExoBundle:Response')
                               ->getAlreadyResponded($paper->getId(), $interTimedQcm[0]->getInteraction()->getId());

                if (count($response) == 0) {
                    //INSERT Response
                    $response = new Response();
                    $response->setNbTries(1);
                    $response->setPaper($paper);
                    $response->setInteraction($em->getRepository('UJMExoBundle:Interaction')->find($interTimedQcm[0]->getInteraction()->getId()));
                } else {
                    //UPDATE Response
                    $response = $response[0];
                    $response->setNbTries($response->getNbTries() + 1);
                }

                $response->setIp($ip);
                $score = explode('/', $res['score']);
                $response->setMark($score[0]);
                $response->setResponse($res['response']);

                $em->persist($response);
                $em->flush();
            }

            $vars['score']    = $res['score'];
            $vars['rightChoices'] = $res['rightChoices'];
            $vars['penalty']  = $res['penalty'];
            $vars['interTimedQcm'] = $res['interTimedQcm'];
            $vars['response'] = $res['response'];
            $vars['exoID']    = $exoID;

            $response = new JsonResponse($vars['rightChoices']);
            return $response;

        }
        $response = new JsonResponse(array('data' => "AJAX REQUEST ERROR"));
        return $response;
    }
}