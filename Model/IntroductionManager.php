<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\IntroductionEvent;
use N1c0\DissertationBundle\Event\IntroductionPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidIntroductionException;

/**
 * Abstract Introduction Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class IntroductionManager implements IntroductionManagerInterface
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
     * Get a list of Introductions.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset)
    {
        return $this->repository->findBy(array(), array('createdAt' => 'DESC'), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return IntroductionInterface
     */
    public function findIntroductionById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty introduction instance
     *
     * @return Introduction
     */
    public function createIntroduction(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $introduction = new $class;

        $introduction->setDissertation($dissertation);

        $event = new IntroductionEvent($introduction);
        $this->dispatcher->dispatch(Events::INTRODUCTION_CREATE, $event);

        return $introduction;
    }

    /**
     * Saves a introduction to the persistence backend used. Each backend
     * must implement the abstract doSaveIntroduction method which will
     * perform the saving of the introduction to the backend.
     *
     * @param  IntroductionInterface         $introduction
     * @throws InvalidIntroductionException when the introduction does not have a dissertation.
     */
    public function saveIntroduction(IntroductionInterface $introduction)
    {
        if (null === $introduction->getDissertation()) {
            throw new InvalidIntroductionException('The introduction must have a dissertation');
        }

        $event = new IntroductionPersistEvent($introduction);
        $this->dispatcher->dispatch(Events::INTRODUCTION_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveIntroduction($introduction);

        $event = new IntroductionEvent($introduction);
        $this->dispatcher->dispatch(Events::INTRODUCTION_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a introduction.
     *
     * @abstract
     * @param IntroductionInterface $introduction
     */
    abstract protected function doSaveIntroduction(IntroductionInterface $introduction);
}
