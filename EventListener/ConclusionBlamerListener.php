<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\ConclusionEvent;
use N1c0\DissertationBundle\Model\SignedConclusionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a conclusion using Symfony2 security component
 */
class ConclusionBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Conclusion.
     *
     * @param  \N1c0\DissertationBundle\Event\ConclusionEvent $event
     * @return void
     */
    public function blame(ConclusionEvent $event)
    {
        $conclusion = $event->getConclusion();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Conclusion Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$conclusion instanceof SignedConclusionInterface) {
            if ($this->logger) {
                $this->logger->debug("Conclusion does not implement SignedConclusionInterface, skipping");
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
            $conclusion->setAuthor($user);
            if (!$conclusion->getAuthors()->contains($user)) {
                $conclusion->addAuthor($user);
            }
            if (!$conclusion->getDissertation()->getAuthors()->contains($user)) {
                $conclusion->getDissertation()->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::CONCLUSION_PRE_PERSIST => 'blame');
    }
}
