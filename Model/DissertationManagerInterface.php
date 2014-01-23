<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Interface to be implemented by element dissertation managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to element dissertation should happen through this interface.
 */
interface DissertationManagerInterface
{
    /**
     * Get a list of Dissertations.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return DissertationInterface
     */
    public function findDissertationById($id);

    /**
     * Finds one element dissertation by the given criteria
     *
     * @param  array           $criteria
     * @return DissertationInterface
     */
    public function findDissertationBy(array $criteria);

    /**
     * Finds dissertations by the given criteria
     *
     * @param array $criteria
     *
     * @return array of DissertationInterface
     */
    public function findDissertationsBy(array $criteria);

    /**
     * Finds all dissertations.
     *
     * @return array of DissertationInterface
     */
    public function findAllDissertations();

    /**
     * Creates an empty element dissertation instance
     *
     * @param  bool   $id
     * @return Dissertation
     */
    public function createDissertation($id = null);

    /**
     * Saves a dissertation
     *
     * @param DissertationInterface $dissertation
     */
    public function saveDissertation(DissertationInterface $dissertation);

    /**
     * Checks if the dissertation was already persisted before, or if it's a new one.
     *
     * @param DissertationInterface $dissertation
     *
     * @return boolean True, if it's a new dissertation
     */
    public function isNewDissertation(DissertationInterface $dissertation);

    /**
     * Returns the element dissertation fully qualified class name
     *
     * @return string
     */
    public function getClass();
}
