<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TransitionInterface;
use N1c0\DissertationBundle\Model\TransitionManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of TransitionManagerInterface and
 * performs Acl checks with the configured Transition Acl service.
 */
class AclTransitionManager implements TransitionManagerInterface
{
    /**
     * The TransitionManager instance to be wrapped with ACL.
     *
     * @var TransitionManagerInterface
     */
    protected $realManager;

    /**
     * The TransitionAcl instance for checking permissions.
     *
     * @var TransitionAclInterface
     */
    protected $transitionAcl;

    /**
     * Constructor.
     *
     * @param TransitionManagerInterface $transitionManager The concrete TransitionManager service
     * @param TransitionAclInterface     $transitionAcl     The Transition Acl service
     */
    public function __construct(TransitionManagerInterface $transitionManager, TransitionAclInterface $transitionAcl)
    {
        $this->realManager = $transitionManager;
        $this->transitionAcl  = $transitionAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $transitions = $this->realManager->all();

        if (!$this->authorizeViewTransition($transitions)) {
            throw new AccessDeniedException();
        }

        return $transitions;
    }

    /**
     * {@inheritDoc}
     */
    public function findTransitionBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findTransitionsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllTransitions(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveTransition(TransitionInterface $transition)
    {
        if (!$this->transitionAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newTransition = $this->isNewTransition($transition);

        if (!$newTransition && !$this->transitionAcl->canEdit($transition)) {
            throw new AccessDeniedException();
        }

        if (($transition::STATE_DELETED === $transition->getState() || $transition::STATE_DELETED === $transition->getPreviousState())
            && !$this->transitionAcl->canDelete($transition)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveTransition($transition);

        if ($newTransition) {
            $this->transitionAcl->setDefaultAcl($transition);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findTransitionById($id)
    {
        $transition = $this->realManager->findTransitionById($id);

        if (null !== $transition && !$this->transitionAcl->canView($transition)) {
            throw new AccessDeniedException();
        }

        return $transition;
    }

    /**
     * {@inheritDoc}
     */
    public function createTransition($id = null)
    {
        return $this->realManager->createTransition($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewTransition(TransitionInterface $transition)
    {
        return $this->realManager->isNewTransition($transition);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the transition have appropriate view permissions.
     *
     * @param  array   $transitions A comment tree
     * @return boolean
     */
    protected function authorizeViewTransition(array $transitions)
    {
        foreach ($transitions as $transition) {
            if (!$this->transitionAcl->canView($transition)) {
                return false;
            }
        }

        return true;
    }
}
