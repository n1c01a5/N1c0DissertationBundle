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
    public function all($limit, $offset);

    /**
     * @param  string          $id
     * @return IntroductionInterface
     */
    public function findIntroductionById($id);

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

    /**
     * Removes a introduction
     *
     * @param IntroductionInterface $introduction
     */
    public function removeIntroduction(IntroductionInterface $introduction);

    /**
     * Checks if the introduction was already persisted before, or if it's a new one.
     *
     * @param IntroductionInterface $introduction
     *
     * @return boolean True, if it's a new introduction
     */
    public function isNewIntroduction(IntroductionInterface $introduction);
}
