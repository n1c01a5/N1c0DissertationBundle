<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Interface to be implemented by introduction managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface IntroductionManagerInterface
{
    /**
     * Get a list of Introductions.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return IntroductionInterface
     */
    public function findIntroductionById($id);

    /**
     * Returns a flat array of introductions with the specified dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of IntroductionInterface
     */
    public function findIntroductionsByDissertation(DissertationInterface $dissertation);

    /**
     * Returns an empty introduction instance
     *
     * @return Introduction
     */
    public function createIntroduction(DissertationInterface $dissertation);

    /**
     * Saves a introduction
     *
     * @param  IntroductionInterface         $introduction
     */
    public function saveIntroduction(IntroductionInterface $introduction);
}
