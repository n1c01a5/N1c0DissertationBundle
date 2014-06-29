<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\ArgumentBundle\Model\ArgumentInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleArgumentAcl implements ArgumentAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Argument object.
     *
     * @var string
     */
    private $argumentClass;

    /**
     * The role that will grant create permission for a argument.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a argument.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a argument.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a argument.
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
     * @param string                   $argumentClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $argumentClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->argumentClass      = $argumentClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Argument.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canView(ArgumentInterface $argument)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent argument.
     *
     * @param  ArgumentInterface|null $parent
     * @return boolean
     */
    public function canReply(ArgumentInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canEdit(ArgumentInterface $argument)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canDelete(ArgumentInterface $argument)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  ArgumentInterface $argument
     * @return void
     */
    public function setDefaultAcl(ArgumentInterface $argument)
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
