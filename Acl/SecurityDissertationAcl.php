<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\SignedDissertationInterface;
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
class SecurityDissertationAcl implements DissertationAclInterface
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
     * The FQCN of the Dissertation object.
     *
     * @var string
     */
    protected $dissertationClass;

    /**
     * The Class OID for the Dissertation object.
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
     * @param string                                   $dissertationClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $dissertationClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->dissertationClass      = $dissertationClass;
        $this->oid               = new ObjectIdentity('class', $this->dissertationClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Dissertation.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canView(DissertationInterface $dissertation)
    {
        return $this->securityContext->isGranted('VIEW', $dissertation);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canEdit(DissertationInterface $dissertation)
    {
        return $this->securityContext->isGranted('EDIT', $dissertation);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canDelete(DissertationInterface $dissertation)
    {
        return $this->securityContext->isGranted('DELETE', $dissertation);
    }

    /**
     * Sets the default object Acl entry for the supplied Dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return void
     */
    public function setDefaultAcl(DissertationInterface $dissertation)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($dissertation);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($dissertation instanceof SignedDissertationInterface &&
            null !== $dissertation->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($dissertation->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Dissertation class.
     *
     * This needs to be re-run whenever the Dissertation class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->dissertationClass);

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
     * `fos:dissertation:installAces --flush` command
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
     * Removes fallback Acl entries for the Dissertation class.
     *
     * This should be run when uninstalling the DissertationBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->dissertationClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

