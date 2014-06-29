<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TransitionEvent;
use N1c0\DissertationBundle\Model\SignedTransitionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a transition using Symfony2 security component
 */
class TransitionBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Transition.
     *
     * @param  \N1c0\DissertationBundle\Event\TransitionEvent $event
     * @return void
     */
    public function blame(TransitionEvent $event)
    {
        $transition = $event->getTransition();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Transition Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$transition instanceof SignedTransitionInterface) {
            if ($this->logger) {
                $this->logger->debug("Transition does not implement SignedTransitionInterface, skipping");
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
            $transition->setAuthor($user);
            if (!$transition->getAuthors()->contains($user)) {
                $transition->addAuthor($user);
            }
            if (!$transition->getDissertation()->getAuthors()->contains($user)) {
                $transition->getDissertation()->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::TRANSITION_PRE_PERSIST => 'blame');
    }
}
