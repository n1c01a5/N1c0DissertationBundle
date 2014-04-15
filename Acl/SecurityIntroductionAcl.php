<?php

namespace N1c0\IntroductionBundle\Acl;

use N1c0\IntroductionBundle\Model\IntroductionInterface;
use N1c0\IntroductionBundle\Model\SignedIntroductionInterface;
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
class SecurityIntroductionAcl implements IntroductionAclInterface
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
     * The FQCN of the Introduction object.
     *
     * @var string
     */
    protected $introductionClass;

    /**
     * The Class OID for the Introduction object.
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
     * @param string                                   $introductionClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $introductionClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->introductionClass      = $introductionClass;
        $this->oid               = new ObjectIdentity('class', $this->introductionClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Introduction.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canView(IntroductionInterface $introduction)
    {
        return $this->securityContext->isGranted('VIEW', $introduction);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canEdit(IntroductionInterface $introduction)
    {
        return $this->securityContext->isGranted('EDIT', $introduction);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canDelete(IntroductionInterface $introduction)
    {
        return $this->securityContext->isGranted('DELETE', $introduction);
    }

    /**
     * Sets the default object Acl entry for the supplied Introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return void
     */
    public function setDefaultAcl(IntroductionInterface $introduction)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($introduction);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($introduction instanceof SignedIntroductionInterface &&
            null !== $introduction->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($introduction->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Introduction class.
     *
     * This needs to be re-run whenever the Introduction class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->introductionClass);

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
     * `fos:introduction:installAces --flush` command
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
     * Removes fallback Acl entries for the Introduction class.
     *
     * This should be run when uninstalling the IntroductionBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->introductionClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

