<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\ConclusionInterface;
use N1c0\DissertationBundle\Model\ConclusionManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of ConclusionManagerInterface and
 * performs Acl checks with the configured Conclusion Acl service.
 */
class AclConclusionManager implements ConclusionManagerInterface
{
    /**
     * The ConclusionManager instance to be wrapped with ACL.
     *
     * @var ConclusionManagerInterface
     */
    protected $realManager;

    /**
     * The ConclusionAcl instance for checking permissions.
     *
     * @var ConclusionAclInterface
     */
    protected $conclusionAcl;

    /**
     * Constructor.
     *
     * @param ConclusionManagerInterface $conclusionManager The concrete ConclusionManager service
     * @param ConclusionAclInterface     $conclusionAcl     The Conclusion Acl service
     */
    public function __construct(ConclusionManagerInterface $conclusionManager, ConclusionAclInterface $conclusionAcl)
    {
        $this->realManager = $conclusionManager;
        $this->conclusionAcl  = $conclusionAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit, $offset)
    {
        $conclusions = $this->realManager->all($limit, $offset);

        if (!$this->authorizeViewConclusion($conclusions)) {
            throw new AccessDeniedException();
        }

        return $conclusions;
    }

    /**
     * {@inheritDoc}
     */
    public function findConclusionBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findConclusionsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllConclusions(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveConclusion(ConclusionInterface $conclusion)
    {
        if (!$this->conclusionAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newConclusion = $this->isNewConclusion($conclusion);

        if (!$newConclusion && !$this->conclusionAcl->canEdit($conclusion)) {
            throw new AccessDeniedException();
        }

        if (($conclusion::STATE_DELETED === $conclusion->getState() || $conclusion::STATE_DELETED === $conclusion->getPreviousState())
            && !$this->conclusionAcl->canDelete($conclusion)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveConclusion($conclusion);

        if ($newConclusion) {
            $this->conclusionAcl->setDefaultAcl($conclusion);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findConclusionById($id)
    {
        $conclusion = $this->realManager->findConclusionById($id);

        if (null !== $conclusion && !$this->conclusionAcl->canView($conclusion)) {
            throw new AccessDeniedException();
        }

        return $conclusion;
    }

    /**
     * {@inheritDoc}
     */
    public function createConclusion($id = null)
    {
        return $this->realManager->createConclusion($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewConclusion(ConclusionInterface $conclusion)
    {
        return $this->realManager->isNewConclusion($conclusion);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the conclusion have appropriate view permissions.
     *
     * @param  array   $conclusions A comment tree
     * @return boolean
     */
    protected function authorizeViewConclusion(array $conclusions)
    {
        foreach ($conclusions as $conclusion) {
            if (!$this->conclusionAcl->canView($conclusion)) {
                return false;
            }
        }

        return true;
    }
}
