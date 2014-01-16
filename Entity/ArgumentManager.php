<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\ArgumentManager as BaseArgumentManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\ArgumentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM ArgumentManager.
 *
 */
class ArgumentManager extends BaseArgumentManager
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
     * Returns a flat array of arguments of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findArgumentsByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('a')
                ->join('a.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->setParameter('dissertation', $dissertation->getId());

        $arguments = $qb
            ->getQuery()
            ->execute();

        return $arguments;
    }

    /**
     * Find one argument by its ID
     *
     * @param  array           $criteria
     * @return ArgumentInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Arguments.
     *
     * @return array of ArgumentInterface
     */
    public function findAllArguments()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the argument. 
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveArgument(ArgumentInterface $argument)
    {
        $this->em->persist($argument->getDissertation());
        $this->em->persist($argument);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified argument dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
