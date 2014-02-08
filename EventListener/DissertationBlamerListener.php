<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\DissertationEvent;
use N1c0\DissertationBundle\Model\SignedDissertationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a dissertation using Symfony2 security component
 */
class DissertationBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Dissertation.
     *
     * @param  \N1c0\DissertationBundle\Event\DissertationEvent $event
     * @return void
     */
    public function blame(DissertationEvent $event)
    {
        $dissertation = $event->getDissertation();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Dissertation Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$dissertation instanceof SignedDissertationInterface) {
            if ($this->logger) {
                $this->logger->debug("Dissertation does not implement SignedDissertationInterface, skipping");
            }

            return;
        }

        if (null === $this->securityContext->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if (null === $dissertation->getAuthor() && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $dissertation->setAuthor($this->securityContext->getToken()->getUser());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::DISSERTATION_PRE_PERSIST => 'blame');
    }
}
