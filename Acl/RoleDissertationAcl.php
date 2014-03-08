<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\DissertationInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleDissertationAcl implements DissertationAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Dissertation object.
     *
     * @var string
     */
    private $dissertationClass;

    /**
     * The role that will grant create permission for a dissertation.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a dissertation.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a dissertation.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a dissertation.
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
     * @param string                   $dissertationClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $dissertationClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->dissertationClass      = $dissertationClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Dissertation.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canView(DissertationInterface $dissertation)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent dissertation.
     *
     * @param  DissertationInterface|null $parent
     * @return boolean
     */
    public function canReply(DissertationInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canEdit(DissertationInterface $dissertation)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canDelete(DissertationInterface $dissertation)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  DissertationInterface $dissertation
     * @return void
     */
    public function setDefaultAcl(DissertationInterface $dissertation)
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
