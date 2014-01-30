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
     * List all arguments.
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
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getArgumentsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('n1c0_dissertation.manager.argument')->all($limit, $offset);
    }

    /**
     * Get single Argument.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Argument for a given id",
     *   output = "N1c0\DissertationBundle\Entity\Argument",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the argument is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="argument")
     *
     * @param int     $id      the argument id
     *
     * @return array
     *
     * @throws NotFoundHttpException when argument not exist
     */
    public function getArgumentAction($id)
    {
        $argument = $this->getOr404($id);

        return $argument;
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
     * @param string $id
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

        return $form;
    }

    /**
     * Create a Argument from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new argument from the submitted data.",
     *   input = "N1c0\DissertationBundle\Form\ArgumentType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Dissertation:newArgument.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the thread
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
                    // Add the argument 
                    $argumentManager->saveArgument($argument);
                
                    $routeOptions = array(
                        'id' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    // Add a method onCreateArgumentSuccess(FormInterface $form)
                    return $this->routeRedirectView('api_1_get_dissertation', $routeOptions, Codes::HTTP_CREATED);
                }
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
     *  template = "N1c0DissertationBundle:Dissertation:editArgument.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the argument id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when argument not exist
     */
    public function putDissertationAction(Request $request, $id)
    {
        try {
            $argument = $this->getOr404($id);

            $form = $this->container->get('n1c0_dissertation.form_factory.argument')->createForm();
            $form->setData($argument);
            $form->bind($request);

            if ($form->isValid()) {
                $argumentManager = $this->container->get('n1c0_dissertation.manager.argument');
                if ($argumentManager->saveArgument($argument) !== false) {
                    $routeOptions = array(
                        'id' => $dissertation->getId(), //get the id of the dissertation (error for the moment) $dissertation not defined
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
     * Update existing argument from the submitted data or create a new argument at a specific location.
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
     *  template = "n1c0DissertationBundle:Dissertation:editArgument.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the argument id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when argument not exist
     */
    public function patchArgumentAction(Request $request, $id)
    {
        try {
            $dissertation = $this->container->get('n1c0_dissertation.argument.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );//TODO Patch dir handler not exists

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
}