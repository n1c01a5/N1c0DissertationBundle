<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\ConclusionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a conclusion.
 */
class ConclusionEvent extends Event
{
    private $conclusion;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\ConclusionInterface $conclusion
     */
    public function __construct(ConclusionInterface $conclusion)
    {
        $this->conclusion = $conclusion;
    }

    /**
     * Returns the conclusion for this event.
     *
     * @return \n1c0\DissertationBundle\Model\ConclusionInterface
     */
    public function getConclusion()
    {
        return $this->conclusion;
    }
}
