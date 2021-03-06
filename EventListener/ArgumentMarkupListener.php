<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\ArgumentEvent;
use N1c0\DissertationBundle\Markup\ParserInterface;
use N1c0\DissertationBundle\Model\RawArgumentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a argument for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class ArgumentMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\DissertationBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw argument data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\DissertationBundle\Event\ArgumentEvent $event
     */
    public function markup(ArgumentEvent $event)
    {
        $argument = $event->getArgument();

        if (!$argument instanceof RawArgumentInterface) {
            return;
        }

        $result = $this->parser->parse($argument->getBody());
        $argument->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::ARGUMENT_PRE_PERSIST => 'markup');
    }
}
