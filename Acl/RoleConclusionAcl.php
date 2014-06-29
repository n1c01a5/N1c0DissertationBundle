<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\ConclusionBundle\Model\ConclusionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleConclusionAcl implements ConclusionAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Conclusion object.
     *
     * @var string
     */
    private $conclusionClass;

    /**
     * The role that will grant create permission for a conclusion.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a conclusion.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a conclusion.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a conclusion.
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
     * @param string                   $conclusionClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $conclusionClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->conclusionClass      = $conclusionClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Conclusion.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canView(ConclusionInterface $conclusion)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent conclusion.
     *
     * @param  ConclusionInterface|null $parent
     * @return boolean
     */
    public function canReply(ConclusionInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canEdit(ConclusionInterface $conclusion)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canDelete(ConclusionInterface $conclusion)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  ConclusionInterface $conclusion
     * @return void
     */
    public function setDefaultAcl(ConclusionInterface $conclusion)
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
