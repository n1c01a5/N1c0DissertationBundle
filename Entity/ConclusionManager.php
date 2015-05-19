<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\ConclusionManager as BaseConclusionManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\ConclusionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM ConclusionManager.
 *
 */
class ConclusionManager extends BaseConclusionManager
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
     * Returns a flat array of conclusions of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findConclusionsByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->join('c.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->add('orderBy', 'c.createdAt DESC')
                ->setParameter('dissertation', $dissertation->getId());

        $conclusions = $qb
            ->getQuery()
            ->execute();

        return $conclusions;
    }

    /**
     * Find one conclusion by its ID
     *
     * @param  array           $criteria
     * @return ConclusionInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Conclusions.
     *
     * @return array of ConclusionInterface
     */
    public function findAllConclusions()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewConclusion(ConclusionInterface $conclusion)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($conclusion);
    }

    /**
     * Performs persisting of the conclusion.
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveConclusion(ConclusionInterface $conclusion)
    {
        $this->em->persist($conclusion->getDissertation());
        $this->em->persist($conclusion);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified conclusion dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
