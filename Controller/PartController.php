<?php

namespace N1c0\DissertationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

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
use N1c0\DissertationBundle\Form\PartType;
use N1c0\DissertationBundle\Model\PartInterface;

class PartController extends FOSRestController
{
    /**
     * Get single Part.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Part for a given id",
     *   output = "N1c0\DissertationBundle\Entity\Part",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the part or the dissertation is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="part")
     *
     * @param int                   $id                   the dissertation id
     * @param int                   $partId           the part id
     *
     * @return array
     *
     * @throws NotFoundHttpException when part not exist
     */
    public function getPartAction($id, $partId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        return $this->getOr404($partId);
    }

    /**
     * Get the parts of a dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing parts.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many parts to return.")
     *
     * @Annotations\View(
     *  templateVar="parts"
     * )
     *
     * @param int                   $id           the dissertation id
     *
     * @return array
     */
    public function getPartsAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_dissertation.manager.part')->findPartsByDissertation($dissertation);
    }

    /**
     * Presents the form to use to create a new part.
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
    public function newPartAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }

        $part = $this->container->get('n1c0_dissertation.manager.part')->createPart($dissertation);

        $form = $this->container->get('n1c0_dissertation.form_factory.part')->createForm();
        $form->setData($part);

        return array(
            'form' => $form,
            'id' => $id
        );
    }

    /**
     * Edits an part.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Part:editPart.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id      the dissertation id
     * @param int     $partId           the part id
     *
     * @return FormTypeInterface
     */
    public function editPartAction($id, $partId)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        $part = $this->getOr404($partId);
        $form = $this->container->get('n1c0_dissertation.form_factory.part')->createForm();
        $form->setData($part);

        return array(
            'form' => $form,
            'id'=>$id,
            'partId' => $part->getId()
        );
    }

    /**
     * Creates a new Part for the Dissertation from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new part for the dissertation from the submitted data.",
     *   input = "N1c0\DissertationBundle\Form\PartType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Part:newPart.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the dissertation
     *
     * @return FormTypeInterface|View
     */
    public function postPartAction(Request $request, $id)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $partManager = $this->container->get('n1c0_dissertation.manager.part');
            $part = $partManager->createPart($dissertation);

            $form = $this->container->get('n1c0_dissertation.form_factory.part')->createForm();
            $form->setData($part);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $partManager->savePart($part);

                    $routeOptions = array(
                        'id' => $id,
                        'partId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;

                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) {
                        // Add a method onCreatePartSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_dissertation_part', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing part from the submitted data or create a new part at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\PartType",
     *   statusCodes = {
     *     201 = "Returned when the Part is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Part:editPart.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation
     * @param int     $partId          the part id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when an entity not exist
     */
    public function putPartAction(Request $request, $id, $partId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $part = $this->getOr404($partId);

            $form = $this->container->get('n1c0_dissertation.form_factory.part')->createForm();
            $form->setData($part);
            $form->bind($request);

            if ($form->isValid()) {
                $partManager = $this->container->get('n1c0_dissertation.manager.part');
                if ($partManager->savePart($part) !== false) {
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

        // Add a method onCreatePartError(FormInterface $form)
        return new Response(sprintf("Error of the part id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Update existing part for a dissertation from the submitted data or create a new part at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\PartType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Part:editDissertationPart.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the dissertation
     * @param int     $partId      the part id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when part not exist
     */
    public function patchPartAction(Request $request, $id, $partId)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
            if (!$dissertation) {
                throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
            }

            $part = $this->getOr404($partId);

            $form = $this->container->get('n1c0_dissertation.form_factory.part')->createForm();
            $form->setData($part);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $partManager = $this->container->get('n1c0_dissertation.manager.part');
                if ($partManager->savePart($part) !== false) {
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
     * Get thread for an part.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a part thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the dissertation id
     * @param int     $partId       the part id
     *
     * @return array
     */
    public function getPartThreadAction($id, $partId)
    {
        return $this->container->get('n1c0_dissertation.comment.dissertation_comment.default')->getThread($partId);
    }

    /**
     * Fetch a Part or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return PartInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($part = $this->container->get('n1c0_dissertation.manager.part')->findPartById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $part;
    }

    /**
     * Get download for the part of the dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download part",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="part")
     *
     * @param int     $id              the dissertation uuid
     * @param int     $partId      the part uuid
     *
     * @return array
     * @throws NotFoundHttpException when dissertation not exist
     * @throws NotFoundHttpException when part not exist
     */
    public function getPartDownloadAction($id, $partId)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource dissertation \'%s\' was not found.',$id));
        }

        if (!($part = $this->container->get('n1c0_dissertation.manager.part')->findPartById($partId))) {
            throw new NotFoundHttpException(sprintf('The resource part \'%s\' was not found.', $partId));
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
            'part'   => $part
        );
    }

    /**
     * Convert the part in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the part",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id              the dissertation uuid
     * @param int     $partId      the part uuid
     * @param string  $format          the format to convert dissertation
     *
     * @return null
     * @throws NotFoundHttpException when dissertation not exist
     * @throws NotFoundHttpException when part not exist
     * @throws FileNotFoundException when file not exist
     */
    public function getPartConvertAction($id, $partId, $format)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource dissertation \'%s\' was not found.',$id));
        }

        if (!($part = $this->container->get('n1c0_dissertation.manager.part')->findPartById($partId))) {
            throw new NotFoundHttpException(sprintf('The resource part \'%s\' was not found.',$partId));
        }

        $partConvert = $this->container->get('n1c0_dissertation.part.download')->getConvert($partId, $format);

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

        if ($ext == "") {$ext = "txt";}
        $filename = $part->getTitle().'.'.$ext;
        $path_file = './uploads/'.$filename;
        $fh = fopen($path_file, "w+");
        if($fh == false) {
            throw new FileNotFoundException($path_file);
        }
        fputs($fh, $partConvert);

        return $this->redirect($_SERVER['SCRIPT_NAME'].'/../uploads/'.$filename);
    }

}
