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


class DissertationController extends FOSRestController
{
    /**
     * List all dissertations.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing dissertations.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many dissertations to return.")
     *
     * @Annotations\View(
     *  templateVar="dissertations"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getDissertationsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('n1c0_dissertation.manager.dissertation')->all($limit, $offset);
    }

    /**
     * Get single Dissertation.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Dissertation for a given id",
     *   output = "N1c0\DissertationBundle\Entity\Dissertation",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the dissertation is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="dissertation")
     *
     * @param int     $id      the dissertation id
     *
     * @return array
     *
     * @throws NotFoundHttpException when dissertation not exist
     */
    public function getDissertationAction($id)
    {
        $dissertation = $this->getOr404($id);

        return $dissertation;
    }

    /**
     * Presents the form to use to create a new dissertation.
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
     * @return FormTypeInterface
     */
    public function newDissertationAction()
    {
        return $form = $this->container->get('n1c0_dissertation.form_factory.dissertation')->createForm();
    }

    /**
     * Create a Dissertation from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new dissertation from the submitted data.",
     *   input = "N1c0\DissertationBundle\Form\DissertationType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Dissertation:newDissertation.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postDissertationAction(Request $request)
    {
        try {
            $dissertationManager = $this->container->get('n1c0_dissertation.manager.dissertation');
            $dissertation = $dissertationManager->createDissertation();

            $form = $this->container->get('n1c0_dissertation.form_factory.dissertation')->createForm();
            $form->setData($dissertation);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    // Add the dissertation 
                    $dissertationManager->saveDissertation($dissertation);
                
                    $routeOptions = array(
                        'id' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    // Add a method onCreateDissertationSuccess(FormInterface $form)
                    return $this->routeRedirectView('api_1_get_dissertation', $routeOptions, Codes::HTTP_CREATED);
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing dissertation from the submitted data or create a new dissertation at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\DissertationType",
     *   statusCodes = {
     *     201 = "Returned when the Dissertation is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Dissertation:editDissertation.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the dissertation id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when dissertation not exist
     */
    public function putDissertationAction(Request $request, $id)
    {
        try {
            $dissertation = $this->getOr404($id);

            $form = $this->container->get('n1c0_dissertation.form_factory.dissertation')->createForm();
            $form->setData($dissertation);
            $form->bind($request);

            if ($form->isValid()) {
                $dissertationManager = $this->container->get('n1c0_dissertation.manager.dissertation');
                if ($dissertationManager->saveDissertation($dissertation) !== false) {
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

        // Add a method onCreateDissertationError(FormInterface $form)
        return new Response(sprintf("Error of the dissertation id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Update existing dissertation from the submitted data or create a new dissertation at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\DissertationType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "n1c0DissertationBundle:Dissertation:editDissertation.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the dissertation id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when dissertation not exist
     */
    public function patchDissertationAction(Request $request, $id)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.dissertation.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $dissertation->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_dissertation', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Dissertation or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return DissertationInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $dissertation;
    }
}
