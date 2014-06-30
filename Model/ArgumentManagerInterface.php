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
     * Get a list of Arguments.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return ArgumentInterface
     */
    public function findArgumentById($id);

    /**
     * Returns a flat array of arguments with the specified dissertation.
     *
     * @param  PartInterface $part
     * @return array           of ArgumentInterface
     */
    public function findArgumentsByPart(PartInterface $part);

    /**
     * Returns an empty argument instance
     *
     * @return Argument
     */
    public function createArgument(PartInterface $part);

    /**
     * Saves a argument
     *
     * @param  ArgumentInterface         $argument
     */
    public function saveArgument(ArgumentInterface $argument);
}
