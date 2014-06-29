<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\TransitionBundle\Events;
use N1c0\TransitionBundle\Event\TransitionEvent;
use N1c0\TransitionBundle\Markup\ParserInterface;
use N1c0\TransitionBundle\Model\RawTransitionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a transition for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class TransitionMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\TransitionBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw transition data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\TransitionBundle\Event\TransitionEvent $event
     */
    public function markup(TransitionEvent $event)
    {
        $transition = $event->getTransition();

        if (!$transition instanceof RawTransitionInterface) {
            return;
        }

        $result = $this->parser->parse($transition->getBody());
        $transition->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::TRANSITION_PRE_PERSIST => 'markup');
    }
}
