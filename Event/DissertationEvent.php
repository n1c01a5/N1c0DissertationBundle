<?php

namespace n1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\DissertationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a dissertation.
 */
class DissertationEvent extends Event
{
    private $dissertation;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\DissertationInterface $dissertation
     */
    public function __construct(DissertationInterface $dissertation)
    {
        $this->dissertation = $dissertation;
    }

    /**
     * Returns the dissertation for this event.
     *
     * @return \n1c0\DissertationBundle\Model\DissertationInterface
     */
    public function getDissertation()
    {
        return $this->dissertation;
    }
}
