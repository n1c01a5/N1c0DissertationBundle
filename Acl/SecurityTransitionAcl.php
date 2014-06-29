<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\TransitionBundle\Model\TransitionInterface;
use N1c0\TransitionBundle\Model\SignedTransitionInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements ACL checking using the Symfony2 Security component
 */
class SecurityTransitionAcl implements TransitionAclInterface
{
    /**
     * Used to retrieve ObjectIdentity instances for objects.
     *
     * @var ObjectIdentityRetrievalStrategyInterface
     */
    protected $objectRetrieval;

    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * The FQCN of the Transition object.
     *
     * @var string
     */
    protected $transitionClass;

    /**
     * The Class OID for the Transition object.
     *
     * @var ObjectIdentity
     */
    protected $oid;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface                 $securityContext
     * @param ObjectIdentityRetrievalStrategyInterface $objectRetrieval
     * @param MutableAclProviderInterface              $aclProvider
     * @param string                                   $transitionClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $transitionClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->transitionClass      = $transitionClass;
        $this->oid               = new ObjectIdentity('class', $this->transitionClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Transition.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canView(TransitionInterface $transition)
    {
        return $this->securityContext->isGranted('VIEW', $transition);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canEdit(TransitionInterface $transition)
    {
        return $this->securityContext->isGranted('EDIT', $transition);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canDelete(TransitionInterface $transition)
    {
        return $this->securityContext->isGranted('DELETE', $transition);
    }

    /**
     * Sets the default object Acl entry for the supplied Transition.
     *
     * @param  TransitionInterface $transition
     * @return void
     */
    public function setDefaultAcl(TransitionInterface $transition)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($transition);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($transition instanceof SignedTransitionInterface &&
            null !== $transition->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($transition->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Transition class.
     *
     * This needs to be re-run whenever the Transition class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->transitionClass);

        try {
            $acl = $this->aclProvider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            return;
        }

        $this->doInstallFallbackAcl($acl, new MaskBuilder());
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs the default Class Ace entries into the provided $acl object.
     *
     * Override this method in a subclass to change what permissions are defined.
     * Once this method has been overridden you need to run the
     * `fos:transition:installAces --flush` command
     *
     * @param  AclInterface $acl
     * @param  MaskBuilder  $builder
     * @return void
     */
    protected function doInstallFallbackAcl(AclInterface $acl, MaskBuilder $builder)
    {
        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());
    }

    /**
     * Removes fallback Acl entries for the Transition class.
     *
     * This should be run when uninstalling the TransitionBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->transitionClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

