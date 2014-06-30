<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\PartBundle\Model\PartInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RolePartAcl implements PartAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Part object.
     *
     * @var string
     */
    private $partClass;

    /**
     * The role that will grant create permission for a part.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a part.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a part.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a part.
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
     * @param string                   $partClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $partClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->partClass      = $partClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Part.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canView(PartInterface $part)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent part.
     *
     * @param  PartInterface|null $parent
     * @return boolean
     */
    public function canReply(PartInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canEdit(PartInterface $part)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canDelete(PartInterface $part)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  PartInterface $part
     * @return void
     */
    public function setDefaultAcl(PartInterface $part)
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
