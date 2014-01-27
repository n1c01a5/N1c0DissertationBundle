<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Interface to be implemented by argument managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface ArgumentManagerInterface
{
    /**
     * Returns a flat array of arguments with the specified dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of ArgumentInterface
     */
    public function findArgumentsByDissertation(DissertationInterface $dissertation);

    /**
     * Returns an empty argument instance
     *
     * @return Argument
     */
    public function createArgument(DissertationInterface $dissertation, DissertationInterface $dissertation);

    /**
     * Saves a argument
     *
     * @param  ArgumentInterface         $argument
     */
    public function saveArgument(ArgumentInterface $argument);
}
