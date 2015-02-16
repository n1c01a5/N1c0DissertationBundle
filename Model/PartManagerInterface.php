<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Interface to be implemented by part managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface PartManagerInterface
{
    /**
     * Get a list of Parts.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset);

    /**
     * @param  string          $id
     * @return PartInterface
     */
    public function findPartById($id);

    /**
     * Returns a flat array of parts with the specified dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of PartInterface
     */
    public function findPartsByDissertation(DissertationInterface $dissertation);

    /**
     * Returns an empty part instance
     *
     * @return Part
     */
    public function createPart(DissertationInterface $dissertation);

    /**
     * Saves a part
     *
     * @param  PartInterface         $part
     */
    public function savePart(PartInterface $part);
}
