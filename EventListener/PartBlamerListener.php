<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\PartEvent;
use N1c0\DissertationBundle\Model\SignedPartInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a part using Symfony2 security component
 */
class PartBlamerListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param LoggerInterface          $logger
     */
    public function __construct(SecurityContextInterface $securityContext = null, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->logger = $logger;
    }

    /**
     * Assigns the currently logged in user to a Part.
     *
     * @param  \N1c0\DissertationBundle\Event\PartEvent $event
     * @return void
     */
    public function blame(PartEvent $event)
    {
        $part = $event->getPart();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Part Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$part instanceof SignedPartInterface) {
            if ($this->logger) {
                $this->logger->debug("Part does not implement SignedPartInterface, skipping");
            }

            return;
        }

        if (null === $this->securityContext->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->securityContext->getToken()->getUser();
            $part->setAuthor($user);
            if (!$part->getAuthors()->contains($user)) {
                $part->addAuthor($user);
            }
            if (!$part->getDissertation()->getAuthors()->contains($user)) {
                $part->getDissertation()->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::PART_PRE_PERSIST => 'blame');
    }
}
