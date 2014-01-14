<?php

namespace N1c0\DissertationBundle\Model;

#use N1c0\DissertationBundle\Events;
#use N1c0\DissertationBundle\Event\DissertationEvent;
#use N1c0\DissertationBundle\Event\DissertationPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidArgumentException;

/**
 * Abstract Argument Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class ArgumentManager implements ArgumentManagerInterface
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
     * Returns an empty argument instance
     *
     * @return Argument
     */
    public function createArgument(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $argument = new $class;

        $argument->setDissertation($dissertation);

        #$event = new ArgumentEvent($argument);
        #$this->dispatcher->dispatch(Events::ARGUMENT_CREATE, $event);

        return $argument;
    }

    /**
     * Saves a argument to the persistence backend used. Each backend
     * must implement the abstract doSaveArgument method which will
     * perform the saving of the argument to the backend.
     *
     * @param  ArgumentInterface         $argument
     * @throws InvalidArgumentException when the argument does not have a dissertation.
     */
    public function saveArgument(ArgumentInterface $argument)
    {
        if (null === $argument->getDissertation()) {
            throw new InvalidArgumentException('The argument must have a dissertation');
        }

        $event = new ArgumentPersistEvent($argument);
        $this->dispatcher->dispatch(Events::ARGUMENT_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveArgument($argument);

        #$event = new ArgumentEvent($argument);
        #$this->dispatcher->dispatch(Events::ARGUMENT_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a argument.
     *
     * @abstract
     * @param ArgumentInterface $argument
     */
    abstract protected function doSaveArgument(ArgumentInterface $argument);
}
