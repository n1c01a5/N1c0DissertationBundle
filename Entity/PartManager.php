<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\PartManager as BasePartManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\PartInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM PartManager.
 *
 */
class PartManager extends BasePartManager
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
     * Returns a flat array of parts of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findPartsByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('p')
                ->join('p.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->add('orderBy', 'p.createdAt DESC')
                ->setParameter('dissertation', $dissertation->getId());

        $parts = $qb
            ->getQuery()
            ->execute();

        return $parts;
    }

    /**
     * Find one part by its ID
     *
     * @param  array           $criteria
     * @return PartInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Parts.
     *
     * @return array of PartInterface
     */
    public function findAllParts()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewPart(PartInterface $part)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($part);
    }

    /**
     * Performs persisting of the part.
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSavePart(PartInterface $part)
    {
        $this->em->persist($part->getDissertation());
        $this->em->persist($part);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified part dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
