<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\PartInterface;
use N1c0\DissertationBundle\Model\SignedPartInterface;
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
class SecurityPartAcl implements PartAclInterface
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
     * The FQCN of the Part object.
     *
     * @var string
     */
    protected $partClass;

    /**
     * The Class OID for the Part object.
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
     * @param string                                   $partClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $partClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->partClass      = $partClass;
        $this->oid               = new ObjectIdentity('class', $this->partClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Part.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canView(PartInterface $part)
    {
        return $this->securityContext->isGranted('VIEW', $part);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canEdit(PartInterface $part)
    {
        return $this->securityContext->isGranted('EDIT', $part);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canDelete(PartInterface $part)
    {
        return $this->securityContext->isGranted('DELETE', $part);
    }

    /**
     * Sets the default object Acl entry for the supplied Part.
     *
     * @param  PartInterface $part
     * @return void
     */
    public function setDefaultAcl(PartInterface $part)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($part);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($part instanceof SignedPartInterface &&
            null !== $part->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($part->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Part class.
     *
     * This needs to be re-run whenever the Part class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->partClass);

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
     * `fos:part:installAces --flush` command
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
     * Removes fallback Acl entries for the Part class.
     *
     * This should be run when uninstalling the PartBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->partClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

