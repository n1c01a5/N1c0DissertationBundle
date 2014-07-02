<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\ConclusionEvent;
use N1c0\DissertationBundle\Markup\ParserInterface;
use N1c0\DissertationBundle\Model\RawConclusionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a conclusion for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class ConclusionMarkupListener implements EventSubscriberInterface
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
     * Parses raw conclusion data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\DissertationBundle\Event\ConclusionEvent $event
     */
    public function markup(ConclusionEvent $event)
    {
        $conclusion = $event->getConclusion();

        if (!$conclusion instanceof RawConclusionInterface) {
            return;
        }

        $result = $this->parser->parse($conclusion->getBody());
        $conclusion->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::CONCLUSION_PRE_PERSIST => 'markup');
    }
}
