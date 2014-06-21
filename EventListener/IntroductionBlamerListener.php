<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\IntroductionEvent;
use N1c0\DissertationBundle\Model\SignedIntroductionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a introduction using Symfony2 security component
 */
class IntroductionBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Introduction.
     *
     * @param  \N1c0\DissertationBundle\Event\IntroductionEvent $event
     * @return void
     */
    public function blame(IntroductionEvent $event)
    {
        $introduction = $event->getIntroduction();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Introduction Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$introduction instanceof SignedIntroductionInterface) {
            if ($this->logger) {
                $this->logger->debug("Introduction does not implement SignedIntroductionInterface, skipping");
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
            $introduction->setAuthor($user);
            if (!$introduction->getAuthors()->contains($user)) {
                $introduction->addAuthor($user);
            }
            if (!$introduction->getDissertation()->getAuthors()->contains($user)) {
                $introduction->getDissertation()->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::INTRODUCTION_PRE_PERSIST => 'blame');
    }
}
