<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Interface to be implemented by transition managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface TransitionManagerInterface
{
    /**
     * Get a list of Transitions.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return TransitionInterface
     */
    public function findTransitionById($id);

    /**
     * Returns a flat array of transitions with the specified dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of TransitionInterface
     */
    public function findTransitionsByDissertation(DissertationInterface $dissertation);

    /**
     * Returns an empty transition instance
     *
     * @return Transition
     */
    public function createTransition(DissertationInterface $dissertation);

    /**
     * Saves a transition
     *
     * @param  TransitionInterface         $transition
     */
    public function saveTransition(TransitionInterface $transition);
}
