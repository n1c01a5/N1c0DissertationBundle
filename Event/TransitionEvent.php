<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\TransitionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a transition.
 */
class TransitionEvent extends Event
{
    private $transition;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\TransitionInterface $transition
     */
    public function __construct(TransitionInterface $transition)
    {
        $this->transition = $transition;
    }

    /**
     * Returns the transition for this event.
     *
     * @return \n1c0\DissertationBundle\Model\TransitionInterface
     */
    public function getTransition()
    {
        return $this->transition;
    }
}
