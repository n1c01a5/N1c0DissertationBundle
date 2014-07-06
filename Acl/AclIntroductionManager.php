<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\IntroductionInterface;
use N1c0\DissertationBundle\Model\IntroductionManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of IntroductionManagerInterface and
 * performs Acl checks with the configured Introduction Acl service.
 */
class AclIntroductionManager implements IntroductionManagerInterface
{
    /**
     * The IntroductionManager instance to be wrapped with ACL.
     *
     * @var IntroductionManagerInterface
     */
    protected $realManager;

    /**
     * The IntroductionAcl instance for checking permissions.
     *
     * @var IntroductionAclInterface
     */
    protected $introductionAcl;

    /**
     * Constructor.
     *
     * @param IntroductionManagerInterface $introductionManager The concrete IntroductionManager service
     * @param IntroductionAclInterface     $introductionAcl     The Introduction Acl service
     */
    public function __construct(IntroductionManagerInterface $introductionManager, IntroductionAclInterface $introductionAcl)
    {
        $this->realManager      = $introductionManager;
        $this->introductionAcl  = $introductionAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $introductions = $this->realManager->all();

        if (!$this->authorizeViewIntroduction($introductions)) {
            throw new AccessDeniedException();
        }

        return $introductions;
    }

    /**
     * {@inheritDoc}
     */
    public function findIntroductionBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findIntroductionsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllIntroductions(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveIntroduction(IntroductionInterface $introduction)
    {
        if (!$this->introductionAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newIntroduction = $this->isNewIntroduction($introduction);

        if (!$newIntroduction && !$this->introductionAcl->canEdit($introduction)) {
            throw new AccessDeniedException();
        }

        if (($introduction::STATE_DELETED === $introduction->getState() || $introduction::STATE_DELETED === $introduction->getPreviousState())
            && !$this->introductionAcl->canDelete($introduction)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveIntroduction($introduction);

        if ($newIntroduction) {
            $this->introductionAcl->setDefaultAcl($introduction);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findIntroductionById($id)
    {
        $introduction = $this->realManager->findIntroductionById($id);

        if (null !== $introduction && !$this->introductionAcl->canView($introduction)) {
            throw new AccessDeniedException();
        }

        return $introduction;
    }

    /**
     * {@inheritDoc}
     */
    public function createIntroduction($id = null)
    {
        return $this->realManager->createIntroduction($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewIntroduction(IntroductionInterface $introduction)
    {
        return $this->realManager->isNewIntroduction($introduction);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the introduction have appropriate view permissions.
     *
     * @param  array   $introductions A comment tree
     * @return boolean
     */
    protected function authorizeViewIntroduction(array $introductions)
    {
        foreach ($introductions as $introduction) {
            if (!$this->introductionAcl->canView($introduction)) {
                return false;
            }
        }

        return true;
    }
}
