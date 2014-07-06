<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\ConclusionInterface;
use N1c0\DissertationBundle\Model\SignedConclusionInterface;
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
class SecurityConclusionAcl implements ConclusionAclInterface
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
     * The FQCN of the Conclusion object.
     *
     * @var string
     */
    protected $conclusionClass;

    /**
     * The Class OID for the Conclusion object.
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
     * @param string                                   $conclusionClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $conclusionClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->conclusionClass      = $conclusionClass;
        $this->oid               = new ObjectIdentity('class', $this->conclusionClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Conclusion.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canView(ConclusionInterface $conclusion)
    {
        return $this->securityContext->isGranted('VIEW', $conclusion);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canEdit(ConclusionInterface $conclusion)
    {
        return $this->securityContext->isGranted('EDIT', $conclusion);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canDelete(ConclusionInterface $conclusion)
    {
        return $this->securityContext->isGranted('DELETE', $conclusion);
    }

    /**
     * Sets the default object Acl entry for the supplied Conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return void
     */
    public function setDefaultAcl(ConclusionInterface $conclusion)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($conclusion);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($conclusion instanceof SignedConclusionInterface &&
            null !== $conclusion->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($conclusion->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Conclusion class.
     *
     * This needs to be re-run whenever the Conclusion class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->conclusionClass);

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
     * `fos:conclusion:installAces --flush` command
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
     * Removes fallback Acl entries for the Conclusion class.
     *
     * This should be run when uninstalling the ConclusionBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->conclusionClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

