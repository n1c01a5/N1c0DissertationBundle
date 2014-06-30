<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\PartInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a part.
 */
class PartEvent extends Event
{
    private $part;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\PartInterface $part
     */
    public function __construct(PartInterface $part)
    {
        $this->part = $part;
    }

    /**
     * Returns the part for this event.
     *
     * @return \n1c0\DissertationBundle\Model\PartInterface
     */
    public function getPart()
    {
        return $this->part;
    }
}
