<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Abstract Dissertation Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class DissertationManager implements DissertationManagerInterface
{
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
        $event = new DissertationEvent($dissertation);
        $this->dispatcher->dispatch(Events::DISSERTATION_PRE_PERSIST, $event);

        $this->doSaveDissertation($dissertation);

        $event = new DissertationEvent($dissertation);
        $this->dispatcher->dispatch(Events::DISSERTATION_POST_PERSIST, $event);
    }

    /**
     * Performs the persistence of the Dissertation.
     *
     * @abstract
     * @param DissertationInterface $dissertation
     */
    abstract protected function doSaveDissertation(DissertationInterface $dissertation);
}
