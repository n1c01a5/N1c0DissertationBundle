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
use N1c0\DissertationBundle\Form\ArgumentType;
use N1c0\DissertationBundle\Model\ArgumentInterface;

class ArgumentController extends FOSRestController
{
    /**
     * Get single Argument.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Argument for a given id",
     *   output = "N1c0\DissertationBundle\Entity\Argument",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the argument or the dissertation is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="argument")
     *
     * @param int                   $id                   the dissertation id
     * @param int                   $argumentId           the argument id
     *
     * @return array
     *
     * @throws NotFoundHttpException when argument not exist
     */
    public function getArgumentAction($id, $argumentId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($argumentId);
    }

    /**
     * Get the arguments of a dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing arguments.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many arguments to return.")
     *
     * @Annotations\View(
     *  templateVar="arguments"
     * )
     *
     * @param int                   $id           the dissertation id
     *
     * @return array
     */
    public function getArgumentsAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_dissertation.manager.argument')->findArgumentsByDissertation($dissertation);
    }

    /**
     * Presents the form to use to create a new argument.
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
    public function newArgumentAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        $argument = $this->container->get('n1c0_dissertation.manager.argument')->createArgument($dissertation);

        $form = $this->container->get('n1c0_dissertation.form_factory.argument')->createForm();
        $form->setData($argument);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an argument.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Argument:editArgument.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id      the dissertation id
     * @param int     $argumentId           the argument id
     *
     * @return FormTypeInterface
     */
    public function editArgumentAction($id, $argumentId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        $argument = $this->getOr404($argumentId);
        $form = $this->container->get('n1c0_dissertation.form_factory.argument')->createForm();
        $form->setData($argument);
    
        return array(
            'form' => $form,
            'id'=>$id,
            'argumentId' => $argument->getId()
        );
    }

    /**
     * Creates a new Argument for the Dissertation from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new argument for the dissertation from the submitted data.",
     *   input = "N1c0\DissertationBundle\Form\ArgumentType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Argument:newArgument.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the dissertation 
     *
     * @return FormTypeInterface|View
     */
    public function postArgumentAction(Request $request, $id)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $argumentManager = $this->container->get('n1c0_dissertation.manager.argument');
            $argument = $argumentManager->createArgument($dissertation);

            $form = $this->container->get('n1c0_dissertation.form_factory.argument')->createForm();
            $form->setData($argument);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $argumentManager->saveArgument($argument);
                
                    $routeOptions = array(
                        'id' => $id,
                        'argumentId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateArgumentSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_dissertation_argument', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing argument from the submitted data or create a new argument at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\ArgumentType",
     *   statusCodes = {
     *     201 = "Returned when the Argument is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Argument:editArgument.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation 
     * @param int     $argumentId      the argument id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when argument not exist
     */
    public function putArgumentAction(Request $request, $id, $argumentId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $argument = $this->getOr404($argumentId);

            $form = $this->container->get('n1c0_dissertation.form_factory.argument')->createForm();
            $form->setData($argument);
            $form->bind($request);

            if ($form->isValid()) {
                $argumentManager = $this->container->get('n1c0_dissertation.manager.argument');
                if ($argumentManager->saveArgument($argument) !== false) {
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

        // Add a method onCreateArgumentError(FormInterface $form)
        return new Response(sprintf("Error of the argument id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Update existing argument for a dissertation from the submitted data or create a new argument at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\ArgumentType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Argument:editDissertationArgument.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation 
     * @param int     $argumentId      the argument id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when argument not exist
     */
    public function patchArgumentAction(Request $request, $id, $argumentId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $argument = $this->getOr404($argumentId);

            $form = $this->container->get('n1c0_dissertation.form_factory.argument')->createForm();
            $form->setData($argument);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $argumentManager = $this->container->get('n1c0_dissertation.manager.argument');
                if ($argumentManager->saveArgument($argument) !== false) {
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
     * Get thread for an argument.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a argument thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the dissertation id
     * @param int     $argumentId       the argument id
     *
     * @return array
     */
    public function getArgumentThreadAction($id, $argumentId)
    {
        return $this->container->get('n1c0_dissertation.comment.dissertation_comment.default')->getThread($argumentId);
    }

    /**
     * Fetch a Argument or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return ArgumentInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($argument = $this->container->get('n1c0_dissertation.manager.argument')->findArgumentById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $argument;
    }

    /**
     * Get download for the argument of the dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download argument",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="argument")
     *
     * @param int     $id              the dissertation uuid
     * @param int     $argumentId      the argument uuid
     *
     * @return array
     * @throws NotFoundHttpException when dissertation not exist
     * @throws NotFoundHttpException when argument not exist
     */
    public function getArgumentDownloadAction($id, $argumentId)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource dissertation \'%s\' was not found.',$id));
        }

        if (!($argument = $this->container->get('n1c0_dissertation.manager.argument')->findArgumentById($argumentId))) {
            throw new NotFoundHttpException(sprintf('The resource argument \'%s\' was not found.', $argumentId));
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
            'argument'   => $argument
        );
    }

    /**
     * Convert the argument in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the argument",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id              the dissertation uuid
     * @param int     $argumentId      the argument uuid
     * @param string  $format          the format to convert dissertation 
     *
     * @return Response
     * @throws NotFoundHttpException when dissertation not exist
     * @throws NotFoundHttpException when argument not exist
     */
    public function getArgumentConvertAction($id, $argumentId, $format)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource dissertation \'%s\' was not found.',$id));
        }

        if (!($argument = $this->container->get('n1c0_dissertation.manager.argument')->findArgumentById($argumentId))) {
            throw new NotFoundHttpException(sprintf('The resource argument \'%s\' was not found.',$argumentId));
        }

        $argumentConvert = $this->container->get('n1c0_dissertation.argument.download')->getConvert($argumentId, $format);

        $response = new Response();
        $response->setContent($argumentConvert);
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
        
        $response->headers->set('Content-disposition', 'filename='.$argument->getTitle().'.'.$ext);
         
        return $response;
    }

}
