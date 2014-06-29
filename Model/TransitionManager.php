<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TransitionEvent;
use N1c0\DissertationBundle\Event\TransitionPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidTransitionException;

/**
 * Abstract Transition Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class TransitionManager implements TransitionManagerInterface
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
     * Get a list of Transitions.
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
     * @param  string          $id
     * @return TransitionInterface
     */
    public function findTransitionById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty transition instance
     *
     * @return Transition
     */
    public function createTransition(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $transition = new $class;

        $transition->setDissertation($dissertation);

        $event = new TransitionEvent($transition);
        $this->dispatcher->dispatch(Events::ARGUMENT_CREATE, $event);

        return $transition;
    }

    /**
     * Saves a transition to the persistence backend used. Each backend
     * must implement the abstract doSaveTransition method which will
     * perform the saving of the transition to the backend.
     *
     * @param  TransitionInterface         $transition
     * @throws InvalidTransitionException when the transition does not have a dissertation.
     */
    public function saveTransition(TransitionInterface $transition)
    {
        if (null === $transition->getDissertation()) {
            throw new InvalidTransitionException('The transition must have a dissertation');
        }

        $event = new TransitionPersistEvent($transition);
        $this->dispatcher->dispatch(Events::ARGUMENT_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveTransition($transition);

        $event = new TransitionEvent($transition);
        $this->dispatcher->dispatch(Events::ARGUMENT_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a transition.
     *
     * @abstract
     * @param TransitionInterface $transition
     */
    abstract protected function doSaveTransition(TransitionInterface $transition);
}
