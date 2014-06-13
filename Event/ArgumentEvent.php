<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\ArgumentInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a argument.
 */
class ArgumentEvent extends Event
{
    private $argument;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\ArgumentInterface $argument
     */
    public function __construct(ArgumentInterface $argument)
    {
        $this->argument = $argument;
    }

    /**
     * Returns the argument for this event.
     *
     * @return \n1c0\DissertationBundle\Model\ArgumentInterface
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
