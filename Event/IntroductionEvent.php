<?php

namespace n1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\IntroductionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a introduction.
 */
class IntroductionEvent extends Event
{
    private $introduction;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\IntroductionInterface $introduction
     */
    public function __construct(IntroductionInterface $introduction)
    {
        $this->introduction = $introduction;
    }

    /**
     * Returns the introduction for this event.
     *
     * @return \n1c0\DissertationBundle\Model\IntroductionInterface
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }
}
