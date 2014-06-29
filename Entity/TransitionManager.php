<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\TransitionManager as BaseTransitionManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\TransitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM TransitionManager.
 *
 */
class TransitionManager extends BaseTransitionManager
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
     * Returns a flat array of transitions of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findTransitionsByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('a')
                ->join('a.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->setParameter('dissertation', $dissertation->getId());

        $transitions = $qb
            ->getQuery()
            ->execute();

        return $transitions;
    }

    /**
     * Find one transition by its ID
     *
     * @param  array           $criteria
     * @return TransitionInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Transitions.
     *
     * @return array of TransitionInterface
     */
    public function findAllTransitions()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the transition. 
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveTransition(TransitionInterface $transition)
    {
        $this->em->persist($transition->getDissertation());
        $this->em->persist($transition);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified transition dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
