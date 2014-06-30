<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\PartBundle\Events;
use N1c0\PartBundle\Event\PartEvent;
use N1c0\PartBundle\Markup\ParserInterface;
use N1c0\PartBundle\Model\RawPartInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a part for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class PartMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\PartBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw part data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\PartBundle\Event\PartEvent $event
     */
    public function markup(PartEvent $event)
    {
        $part = $event->getPart();

        if (!$part instanceof RawPartInterface) {
            return;
        }

        $result = $this->parser->parse($part->getBody());
        $part->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::PART_PRE_PERSIST => 'markup');
    }
}
