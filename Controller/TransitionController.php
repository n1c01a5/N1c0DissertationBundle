<?php

namespace N1c0\DissertationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use N1c0\DissertationBundle\Exception\InvalidFormException;
use N1c0\DissertationBundle\Form\DissertationType;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Form\TransitionType;
use N1c0\DissertationBundle\Model\TransitionInterface;

class TransitionController extends FOSRestController
{
    /**
     * Get single Transition.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Transition for a given id",
     *   output = "N1c0\DissertationBundle\Entity\Transition",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the transition or the dissertation is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="transition")
     *
     * @param int                   $id                   the dissertation id
     * @param int                   $transitionId           the transition id
     *
     * @return array
     *
     * @throws NotFoundHttpException when transition not exist
     */
    public function getTransitionAction($id, $transitionId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($transitionId);
    }

    /**
     * Get the transitions of a dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing transitions.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many transitions to return.")
     *
     * @Annotations\View(
     *  templateVar="transitions"
     * )
     *
     * @param int                   $id           the dissertation id
     *
     * @return array
     */
    public function getTransitionsAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_dissertation.manager.transition')->findTransitionsByDissertation($dissertation);
    }

    /**
     * Presents the form to use to create a new transition.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  templateVar = "form"
     * )
     *
     * @param int                   $id           the dissertation id
     *
     * @return FormTypeInterface
     */
    public function newTransitionAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        $transition = $this->container->get('n1c0_dissertation.manager.transition')->createTransition($dissertation);

        $form = $this->container->get('n1c0_dissertation.form_factory.transition')->createForm();
        $form->setData($transition);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an transition.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Transition:editTransition.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id      the dissertation id
     * @param int     $transitionId           the transition id
     *
     * @return FormTypeInterface
     */
    public function editTransitionAction($id, $transitionId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        $transition = $this->getOr404($transitionId);
        $form = $this->container->get('n1c0_dissertation.form_factory.transition')->createForm();
        $form->setData($transition);
    
        return array(
            'form' => $form,
            'id'=>$id,
            'transitionId' => $transition->getId()
        );
    }

    /**
     * Creates a new Transition for the Dissertation from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new transition for the dissertation from the submitted data.",
     *   input = "N1c0\DissertationBundle\Form\TransitionType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Transition:newTransition.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the dissertation 
     *
     * @return FormTypeInterface|View
     */
    public function postTransitionAction(Request $request, $id)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $transitionManager = $this->container->get('n1c0_dissertation.manager.transition');
            $transition = $transitionManager->createTransition($dissertation);

            $form = $this->container->get('n1c0_dissertation.form_factory.transition')->createForm();
            $form->setData($transition);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $transitionManager->saveTransition($transition);
                
                    $routeOptions = array(
                        'id' => $id,
                        'transitionId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateTransitionSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_dissertation_transition', $routeOptions, Codes::HTTP_CREATED);
                    }
                } else {
                    $response['success'] = false;
                }
                return new JsonResponse( $response );
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing transition from the submitted data or create a new transition at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\TransitionType",
     *   statusCodes = {
     *     201 = "Returned when the Transition is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Transition:editTransition.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation 
     * @param int     $transitionId      the transition id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when transition not exist
     */
    public function putTransitionAction(Request $request, $id, $transitionId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $transition = $this->getOr404($transitionId);

            $form = $this->container->get('n1c0_dissertation.form_factory.transition')->createForm();
            $form->setData($transition);
            $form->bind($request);

            if ($form->isValid()) {
                $transitionManager = $this->container->get('n1c0_dissertation.manager.transition');
                if ($transitionManager->saveTransition($transition) !== false) {
                    $routeOptions = array(
                        'id' => $dissertation->getId(),                  
                        '_format' => $request->get('_format')
                    );

                    return $this->routeRedirectView('api_1_get_dissertation', $routeOptions, Codes::HTTP_OK);
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        // Add a method onCreateTransitionError(FormInterface $form)
        return new Response(sprintf("Error of the transition id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Update existing transition for a dissertation from the submitted data or create a new transition at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\TransitionType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Transition:editDissertationTransition.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation 
     * @param int     $transitionId      the transition id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when transition not exist
     */
    public function patchTransitionAction(Request $request, $id, $transitionId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $transition = $this->getOr404($transitionId);

            $form = $this->container->get('n1c0_dissertation.form_factory.transition')->createForm();
            $form->setData($transition);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $transitionManager = $this->container->get('n1c0_dissertation.manager.transition');
                if ($transitionManager->saveTransition($transition) !== false) {
                    $routeOptions = array(
                        'id' => $dissertation->getId(),                  
                        '_format' => $request->get('_format')
                    );

                    return $this->routeRedirectView('api_1_get_dissertation', $routeOptions, Codes::HTTP_CREATED);
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }   
    }

    /**
     * Get thread for an transition.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a transition thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the dissertation id
     * @param int     $transitionId       the transition id
     *
     * @return array
     */
    public function getTransitionThreadAction($id, $transitionId)
    {
        return $this->container->get('n1c0_dissertation.comment.dissertation_comment.default')->getThread($transitionId);
    }

    /**
     * Fetch a Transition or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return TransitionInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($transition = $this->container->get('n1c0_dissertation.manager.transition')->findTransitionById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $transition;
    }

    /**
     * Get download for the transition of the dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download transition",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="transition")
     *
     * @param int     $id              the dissertation uuid
     * @param int     $transitionId      the transition uuid
     *
     * @return array
     * @throws NotFoundHttpException when dissertation not exist
     * @throws NotFoundHttpException when transition not exist
     */
    public function getTransitionDownloadAction($id, $transitionId)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource dissertation \'%s\' was not found.',$id));
        }

        if (!($transition = $this->container->get('n1c0_dissertation.manager.transition')->findTransitionById($transitionId))) {
            throw new NotFoundHttpException(sprintf('The resource transition \'%s\' was not found.', $transitionId));
        }

        $formats = array(
            "native",
            "json",
            "docx",
            "odt",
            "epub",
            "epub3",
            "fb2",
            "html",
            "html5",
            "slidy",
            "dzslides",
            "docbook",
            "opendocument",
            "latex",
            "beamer",
            "context",
            "texinfo",
            "markdown",
            "pdf",
            "plain",
            "rst",
            "mediawiki",
            "textile",
            "rtf",
            "org",
            "asciidoc"
        );

        return array(
            'formats'    => $formats, 
            'transition'   => $transition
        );
    }

    /**
     * Convert the transition in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the transition",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id              the dissertation uuid
     * @param int     $transitionId      the transition uuid
     * @param string  $format          the format to convert dissertation 
     *
     * @return Response
     * @throws NotFoundHttpException when dissertation not exist
     * @throws NotFoundHttpException when transition not exist
     */
    public function getTransitionConvertAction($id, $transitionId, $format)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource dissertation \'%s\' was not found.',$id));
        }

        if (!($transition = $this->container->get('n1c0_dissertation.manager.transition')->findTransitionById($transitionId))) {
            throw new NotFoundHttpException(sprintf('The resource transition \'%s\' was not found.',$transitionId));
        }

        $transitionConvert = $this->container->get('n1c0_dissertation.transition.download')->getConvert($transitionId, $format);

        $response = new Response();
        $response->setContent($transitionConvert);
        $response->headers->set('Content-Type', 'application/force-download');
        switch ($format) {
            case "native":
                $ext = "";
            break;
            case "s5":
                $ext = "html";
            break;
            case "slidy":
                $ext = "html";
            break;
            case "slideous":
                $ext = "html";
            break;
            case "dzslides":
                $ext = "html";
            break;
            case "latex":
                $ext = "tex";
            break;
            case "context":
                $ext = "tex";
            break;
            case "beamer":
                $ext = "pdf";
            break;
            case "rst":
                $ext = "text";
            break;
            case "docbook":
                $ext = "db";
            break;
            case "man":
                $ext = "";
            break;
            case "asciidoc":
                $ext = "txt";
            break;
            case "markdown":
                $ext = "md";
            break;
            case "epub3":
                $ext = "epub";
            break;
            default:
                $ext = $format;       
        }
        
        $response->headers->set('Content-disposition', 'filename='.$transition->getTitle().'.'.$ext);
         
        return $response;
    }

}
