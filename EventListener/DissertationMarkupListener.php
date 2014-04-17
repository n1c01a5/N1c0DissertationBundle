<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\DissertationEvent;
use N1c0\DissertationBundle\Markup\ParserInterface;
use N1c0\DissertationBundle\Model\RawDissertationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a dissertation for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class DissertationMarkupListener implements EventSubscriberInterface
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
     * Parses raw dissertation data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\DissertationBundle\Event\DissertationEvent $event
     */
    public function markup(DissertationEvent $event)
    {
        $dissertation = $event->getDissertation();

        if (!$dissertation instanceof RawDissertationInterface) {
            return;
        }

        $result = $this->parser->parse($dissertation->getBody());
        $dissertation->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::DISSERTATION_PRE_PERSIST => 'markup');
    }
}
