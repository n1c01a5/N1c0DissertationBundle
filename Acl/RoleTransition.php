<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\TransitionBundle\Model\TransitionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleTransitionAcl implements TransitionAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Transition object.
     *
     * @var string
     */
    private $transitionClass;

    /**
     * The role that will grant create permission for a transition.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a transition.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a transition.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a transition.
     *
     * @var string
     */
    private $deleteRole;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param string                   $createRole
     * @param string                   $viewRole
     * @param string                   $editRole
     * @param string                   $deleteRole
     * @param string                   $transitionClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $transitionClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->transitionClass      = $transitionClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Transition.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canView(TransitionInterface $transition)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent transition.
     *
     * @param  TransitionInterface|null $parent
     * @return boolean
     */
    public function canReply(TransitionInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canEdit(TransitionInterface $transition)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canDelete(TransitionInterface $transition)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  TransitionInterface $transition
     * @return void
     */
    public function setDefaultAcl(TransitionInterface $transition)
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function installFallbackAcl()
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {

    }
}
