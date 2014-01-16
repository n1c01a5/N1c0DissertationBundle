<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\DissertationManager as BaseDissertationManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM DissertationManager.
 *
 */
class DissertationManager extends BaseDissertationManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        parent::__construct($dispatcher);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Finds one element dissertation by the given criteria
     *
     * @param  array           $criteria
     * @return DissertationInterface
     */
    public function findDissertationBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findDissertationsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Finds all dissertations.
     *
     * @return array of DissertationInterface
     */
    public function findAllDissertations()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewDissertation(DissertationInterface $dissertation)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($dissertation);
    }

    /**
     * Saves a dissertation
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveDissertation(DissertationInterface $dissertation)
    {
        $this->em->persist($dissertation);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified element dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
