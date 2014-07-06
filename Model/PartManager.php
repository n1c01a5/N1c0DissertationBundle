<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\PartEvent;
use N1c0\DissertationBundle\Event\PartPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidPartException;

/**
 * Abstract Part Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class PartManager implements PartManagerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get a list of Parts.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), array('createdAt' => 'DESC'), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return PartInterface
     */
    public function findPartById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty part instance
     *
     * @return Part
     */
    public function createPart(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $part = new $class;

        $part->setDissertation($dissertation);

        $event = new PartEvent($part);
        $this->dispatcher->dispatch(Events::PART_CREATE, $event);

        return $part;
    }

    /**
     * Saves a part to the persistence backend used. Each backend
     * must implement the abstract doSavePart method which will
     * perform the saving of the part to the backend.
     *
     * @param  PartInterface         $part
     * @throws InvalidPartException when the part does not have a dissertation.
     */
    public function savePart(PartInterface $part)
    {
        if (null === $part->getDissertation()) {
            throw new InvalidPartException('The part must have a dissertation');
        }

        $event = new PartPersistEvent($part);
        $this->dispatcher->dispatch(Events::PART_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSavePart($part);

        $event = new PartEvent($part);
        $this->dispatcher->dispatch(Events::PART_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a part.
     *
     * @abstract
     * @param PartInterface $part
     */
    abstract protected function doSavePart(PartInterface $part);
}
