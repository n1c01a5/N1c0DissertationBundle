<?php

namespace N1c0\ArgumentBundle\Model;

/**
 * Interface to be implemented by argument managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to arguments should happen through this interface.
 */
interface ArgumentManagerInterface
{
    /**
     * Returns a flat array of arguments with the specified dissertation.
     *
     * The sorter parameter should be left alone if you are sorting in the
     * tree methods.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of ArgumentInterface
     */
    public function findArgumentsByDissertation(DissertationInterface $dissertation);

    /**
     * Saves a argument.
     *
     * @param ArgumentInterface $argument
     */
    public function saveArgument(ArgumentInterface $argument);

    /**
     * Find one argument by its ID.
     *
     * @return Argument or null
     */
    public function findArgumentById($id);

    /**
     * Creates an empty argument instance.
     *
     * @return Argument
     */
    public function createArgument(DissertationInterface $dissertation, ArgumentInterface $argument = null);

    /**
     * Checks if the argument was already persisted before, or if it's a new one.
     *
     * @param ArgumentInterface $argument
     *
     * @return boolean True, if it's a new argument
     */
    public function isNewArgument(ArgumentInterface $argument);

    /**
     * Returns the argument fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}
