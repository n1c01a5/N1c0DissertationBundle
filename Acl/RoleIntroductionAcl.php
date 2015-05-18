<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\IntroductionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleIntroductionAcl implements IntroductionAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Introduction object.
     *
     * @var string
     */
    private $introductionClass;

    /**
     * The role that will grant create permission for a introduction.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a introduction.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a introduction.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a introduction.
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
     * @param string                   $introductionClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $introductionClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->introductionClass      = $introductionClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Introduction.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canView(IntroductionInterface $introduction)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent introduction.
     *
     * @param  IntroductionInterface|null $parent
     * @return boolean
     */
    public function canReply(IntroductionInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canEdit(IntroductionInterface $introduction)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canDelete(IntroductionInterface $introduction)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  IntroductionInterface $introduction
     * @return void
     */
    public function setDefaultAcl(IntroductionInterface $introduction)
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
