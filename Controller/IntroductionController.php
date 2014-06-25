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
use N1c0\DissertationBundle\Form\IntroductionType;
use N1c0\DissertationBundle\Model\IntroductionInterface;

class IntroductionController extends FOSRestController
{
    /**
     * Get single Introduction.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Introduction for a given id",
     *   output = "N1c0\DissertationBundle\Entity\Introduction",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the introduction or the dissertation is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="introduction")
     *
     * @param int                   $id                   the dissertation id
     * @param int                   $introductionId           the introduction id
     *
     * @return array
     *
     * @throws NotFoundHttpException when introduction not exist
     */
    public function getIntroductionAction($id, $introductionId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($introductionId);
    }

    /**
     * Get the introductions of a dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing introductions.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many introductions to return.")
     *
     * @Annotations\View(
     *  templateVar="introductions"
     * )
     *
     * @param int                   $id           the dissertation id
     *
     * @return array
     */
    public function getIntroductionsAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_dissertation.manager.introduction')->findIntroductionsByDissertation($dissertation);
    }

    /**
     * Presents the form to use to create a new introduction.
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
    public function newIntroductionAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        $introduction = $this->container->get('n1c0_dissertation.manager.introduction')->createIntroduction($dissertation);

        $form = $this->container->get('n1c0_dissertation.form_factory.introduction')->createForm();
        $form->setData($introduction);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an introduction.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Introduction:editIntroduction.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the dissertation id
     * @param int     $introductionId           the introduction id
     *
     * @return FormTypeInterface
     */
    public function editIntroductionAction($id, $introductionId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        $introduction = $this->getOr404($introductionId);

        $form = $this->container->get('n1c0_dissertation.form_factory.introduction')->createForm();
        $form->setData($introduction);
    
        return array(
            'form'           => $form,
            'id'             => $id,
            'introductionId' => $introduction->getId()
        );
    }


    /**
     * Creates a new Introduction for the Dissertation from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new introduction for the dissertation from the submitted data.",
     *   input = "N1c0\DissertationBundle\Form\IntroductionType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Introduction:newIntroduction.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the dissertation 
     *
     * @return FormTypeInterface|View
     */
    public function postIntroductionAction(Request $request, $id)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $introductionManager = $this->container->get('n1c0_dissertation.manager.introduction');
            $introduction = $introductionManager->createIntroduction($dissertation);

            $form = $this->container->get('n1c0_dissertation.form_factory.introduction')->createForm();
            $form->setData($introduction);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $introductionManager->saveIntroduction($introduction);
                
                    $routeOptions = array(
                        'id' => $id,
                        'introductionId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateIntroductionSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_dissertation_introduction', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing introduction from the submitted data or create a new introduction at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\IntroductionType",
     *   statusCodes = {
     *     201 = "Returned when the Introduction is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Introduction:editDissertationIntroduction.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation 
     * @param int     $introductionId      the introduction id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when introduction not exist
     */
    public function putIntroductionAction(Request $request, $id, $introductionId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $introduction = $this->getOr404($introductionId);

            $form = $this->container->get('n1c0_dissertation.form_factory.introduction')->createForm();
            $form->setData($introduction);
            $form->bind($request);

            if ($form->isValid()) {
                $introductionManager = $this->container->get('n1c0_dissertation.manager.introduction');
                if ($introductionManager->saveIntroduction($introduction) !== false) {
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
    }

    /**
     * Update existing introduction for a dissertation from the submitted data or create a new introduction at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\IntroductionType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Introduction:editDissertationIntroduction.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation 
     * @param int     $introductionId      the introduction id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when introduction not exist
     */
    public function patchIntroductionAction(Request $request, $id, $introductionId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $introduction = $this->getOr404($introductionId);

            $form = $this->container->get('n1c0_dissertation.form_factory.introduction')->createForm();
            $form->setData($introduction);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $introductionManager = $this->container->get('n1c0_dissertation.manager.introduction');
                if ($introductionManager->saveIntroduction($introduction) !== false) {
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
     * Get thread for an introduction.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a introduction thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the dissertation id
     * @param int     $introductionId       the introduction id
     *
     * @return array
     */
    public function getIntroductionThreadAction($id, $introductionId)
    {
        return $this->container->get('n1c0_dissertation.comment.dissertation_comment.default')->getThread($introductionId);
    }

    /**
     * Fetch a Introduction or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return IntroductionInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($introduction = $this->container->get('n1c0_dissertation.manager.introduction')->findIntroductionById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $introduction;
    }

    /**
     * Get download for the introduction.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download introduction",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="introduction")
     *
     * @param int     $id                  the dissertation uuid
     * @param int     $introductionId      the introduction uuid
     *
     * @return array
     * @throws NotFoundHttpException when dissertation not exist
     */
    public function getIntroductionDownloadAction($id, $introductionId)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($introduction = $this->container->get('n1c0_dissertation.manager.introduction')->findIntroductionById($introductionId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
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
            'formats'        => $formats, 
            'id'             => $id,
            'introductionId' => $introductionId
        );
    }

    /**
     * Convert the introduction in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the introduction",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the dissertation uuid
     * @param int     $introductionId      the introduction uuid
     * @param string  $format              the format to convert dissertation 
     *
     * @return Response
     * @throws NotFoundHttpException when dissertation not exist
     */
    public function getIntroductionConvertAction($id, $introductionId, $format)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The dissertation with the id \'%s\' was not found.',$id));
        }

        if (!($introduction = $this->container->get('n1c0_dissertation.manager.introduction')->findIntroductionById($introductionId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $introductionConvert = $this->container->get('n1c0_dissertation.introduction.download')->getConvert($introductionId, $format);

        $response = new Response();
        $response->setContent($introductionConvert);
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
   
        $response->headers->set('Content-disposition', 'filename='.$introduction->getTitle().'.'.$ext);
         
        return $response;
    }

}
