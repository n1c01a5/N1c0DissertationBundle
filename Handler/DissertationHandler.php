<?php

namespace N1c0\DissertationBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Form\DissertationType;
use N1c0\DissertationBundle\Exception\InvalidFormException;

class DissertationHandler implements DissertationHandlerInterface
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Dissertation.
     *
     * @param mixed $id
     *
     * @return DissertationInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Dissertations.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Dissertation.
     *
     * @param array $parameters
     *
     * @return DissertationInterface
     */
    public function post(array $parameters)
    {
        $dissertation = $this->createDissertation();

        return $this->processForm($dissertation, $parameters, 'POST');
    }

    /**
     * Edit a Dissertation.
     *
     * @param DissertationInterface $dissertation
     * @param array         $parameters
     *
     * @return DissertationInterface
     */
    public function put(DissertationInterface $dissertation, array $parameters)
    {
        return $this->processForm($dissertation, $parameters, 'PUT');
    }

    /**
     * Partially update a Dissertation.
     *
     * @param DissertationInterface $dissertation
     * @param array         $parameters
     *
     * @return DissertationInterface
     */
    public function patch(DissertationInterface $dissertation, array $parameters)
    {
        return $this->processForm($dissertation, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param DissertationInterface $dissertation
     * @param array         $parameters
     * @param String        $method
     *
     * @return DissertationInterface
     *
     * @throws \N1c0\DissertationBundle\Exception\InvalidFormException
     */
    private function processForm(DissertationInterface $dissertation, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new DissertationType(), $dissertation, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $dissertation = $form->getData();
            $this->om->persist($dissertation);
            $this->om->flush($dissertation);

            return $dissertation;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createDissertation()
    {
        return new $this->entityClass();
    }

}
