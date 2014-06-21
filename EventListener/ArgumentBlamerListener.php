<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\ArgumentEvent;
use N1c0\DissertationBundle\Model\SignedArgumentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a argument using Symfony2 security component
 */
class ArgumentBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Argument.
     *
     * @param  \N1c0\DissertationBundle\Event\ArgumentEvent $event
     * @return void
     */
    public function blame(ArgumentEvent $event)
    {
        $argument = $event->getArgument();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Argument Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$argument instanceof SignedArgumentInterface) {
            if ($this->logger) {
                $this->logger->debug("Argument does not implement SignedArgumentInterface, skipping");
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
            $argument->setAuthor($user);
            if (!$argument->getAuthors()->contains($user)) {
                $argument->addAuthor($user);
            }
            if (!$argument->getDissertation()->getAuthors()->contains($user)) {
                $argument->getDissertation()->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::ARGUMENT_PRE_PERSIST => 'blame');
    }
}
