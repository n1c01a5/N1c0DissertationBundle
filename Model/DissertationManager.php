<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\DissertationEvent;
use N1c0\DissertationBundle\Event\DissertationPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Dissertation Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class DissertationManager implements DissertationManagerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \N1c0\DissertationBundle\Sorting\SortingFactory                   $factory
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param  string          $id
     * @return DissertationInterface
     */
    public function findDissertationById($id)
    {
        return $this->findDissertationBy(array('id' => $id));
    }

    /**
     * Creates an empty element dissertation instance
     *
     * @return Dissertation
     */
    public function createDissertation($id = null)
    {
        $class = $this->getClass();
        $dissertation = new $class;

        if (null !== $id) {
            $dissertation->setId($id);
        }

        $event = new DissertationEvent($dissertation);
        $this->dispatcher->dispatch(Events::DISSERTATION_CREATE, $event);

        return $dissertation;
    }

    /**
     * Persists a dissertation.
     *
     * @param DissertationInterface $dissertation
     */
    public function saveDissertation(DissertationInterface $dissertation)
    {
        $event = new DissertationPersistEvent($dissertation);
        $this->dispatcher->dispatch(Events::DISSERTATION_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveDissertation($dissertation);

        $event = new DissertationEvent($dissertation);
        $this->dispatcher->dispatch(Events::DISSERTATION_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of the Dissertation.
     *
     * @abstract
     * @param DissertationInterface $dissertation
     */
    abstract protected function doSaveDissertation(DissertationInterface $dissertation);
}
