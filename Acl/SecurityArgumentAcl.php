<?php

namespace N1c0\ArgumentBundle\Acl;

use N1c0\ArgumentBundle\Model\ArgumentInterface;
use N1c0\ArgumentBundle\Model\SignedArgumentInterface;
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
class SecurityArgumentAcl implements ArgumentAclInterface
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
     * The FQCN of the Argument object.
     *
     * @var string
     */
    protected $argumentClass;

    /**
     * The Class OID for the Argument object.
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
     * @param string                                   $argumentClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $argumentClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->argumentClass      = $argumentClass;
        $this->oid               = new ObjectIdentity('class', $this->argumentClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Argument.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canView(ArgumentInterface $argument)
    {
        return $this->securityContext->isGranted('VIEW', $argument);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canEdit(ArgumentInterface $argument)
    {
        return $this->securityContext->isGranted('EDIT', $argument);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canDelete(ArgumentInterface $argument)
    {
        return $this->securityContext->isGranted('DELETE', $argument);
    }

    /**
     * Sets the default object Acl entry for the supplied Argument.
     *
     * @param  ArgumentInterface $argument
     * @return void
     */
    public function setDefaultAcl(ArgumentInterface $argument)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($argument);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($argument instanceof SignedArgumentInterface &&
            null !== $argument->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($argument->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Argument class.
     *
     * This needs to be re-run whenever the Argument class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->argumentClass);

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
     * `fos:argument:installAces --flush` command
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
     * Removes fallback Acl entries for the Argument class.
     *
     * This should be run when uninstalling the ArgumentBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->argumentClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

