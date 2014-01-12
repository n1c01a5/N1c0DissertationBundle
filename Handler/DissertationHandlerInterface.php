<?php

namespace N1c0\DissertationBundle\Handler;

use N1c0\DissertationBundle\Model\DissertationInterface;

interface DissertationHandlerInterface
{
    /**
     * Get a Dissertation given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return DissertationInterface
     */
    public function get($id);

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
     * Post Dissertation, creates a new Dissertation.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return DissertationInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Dissertation.
     *
     * @api
     *
     * @param DissertationInterface   $dissertation
     * @param array           $parameters
     *
     * @return DissertationInterface
     */
    public function put(DissertationInterface $dissertation, array $parameters);

    /**
     * Partially update a Dissertation.
     *
     * @api
     *
     * @param DissertationInterface   $dissertation
     * @param array           $parameters
     *
     * @return DissertationInterface
     */
    public function patch(DissertationInterface $dissertation, array $parameters);
}
