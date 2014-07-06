<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\ConclusionEvent;
use N1c0\DissertationBundle\Event\ConclusionPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidConclusionException;

/**
 * Abstract Conclusion Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class ConclusionManager implements ConclusionManagerInterface
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
     * Get a list of Conclusions.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), array('createdAt' => 'ASC'), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return ConclusionInterface
     */
    public function findConclusionById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty conclusion instance
     *
     * @return Conclusion
     */
    public function createConclusion(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $conclusion = new $class;

        $conclusion->setDissertation($dissertation);

        $event = new ConclusionEvent($conclusion);
        $this->dispatcher->dispatch(Events::CONCLUSION_CREATE, $event);

        return $conclusion;
    }

    /**
     * Saves a conclusion to the persistence backend used. Each backend
     * must implement the abstract doSaveConclusion method which will
     * perform the saving of the conclusion to the backend.
     *
     * @param  ConclusionInterface         $conclusion
     * @throws InvalidConclusionException when the conclusion does not have a dissertation.
     */
    public function saveConclusion(ConclusionInterface $conclusion)
    {
        if (null === $conclusion->getDissertation()) {
            throw new InvalidConclusionException('The conclusion must have a dissertation');
        }

        $event = new ConclusionPersistEvent($conclusion);
        $this->dispatcher->dispatch(Events::CONCLUSION_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveConclusion($conclusion);

        $event = new ConclusionEvent($conclusion);
        $this->dispatcher->dispatch(Events::CONCLUSION_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a conclusion.
     *
     * @abstract
     * @param ConclusionInterface $conclusion
     */
    abstract protected function doSaveConclusion(ConclusionInterface $conclusion);
}
