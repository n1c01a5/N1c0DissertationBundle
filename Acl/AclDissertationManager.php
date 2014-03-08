<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\DissertationManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of DissertationManagerInterface and
 * performs Acl checks with the configured Dissertation Acl service.
 */
class AclDissertationManager implements DissertationManagerInterface
{
    /**
     * The DissertationManager instance to be wrapped with ACL.
     *
     * @var DissertationManagerInterface
     */
    protected $realManager;

    /**
     * The DissertationAcl instance for checking permissions.
     *
     * @var DissertationAclInterface
     */
    protected $dissertationAcl;

    /**
     * Constructor.
     *
     * @param DissertationManagerInterface $dissertationManager The concrete DissertationManager service
     * @param DissertationAclInterface     $dissertationAcl     The Dissertation Acl service
     */
    public function __construct(DissertationManagerInterface $dissertationManager, DissertationAclInterface $dissertationAcl)
    {
        $this->realManager = $dissertationManager;
        $this->dissertationAcl  = $dissertationAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $dissertations = $this->realManager->all();

        if (!$this->authorizeViewDissertation($dissertations)) {
            throw new AccessDeniedException();
        }

        return $dissertations;
    }

    /**
     * {@inheritDoc}
     */
    public function findDissertationBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findDissertationsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllDissertations(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveDissertation(DissertationInterface $dissertation)
    {
        if (!$this->dissertationAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newDissertation = $this->isNewDissertation($dissertation);

        if (!$newDissertation && !$this->dissertationAcl->canEdit($dissertation)) {
            throw new AccessDeniedException();
        }

        if (($dissertation::STATE_DELETED === $dissertation->getState() || $dissertation::STATE_DELETED === $dissertation->getPreviousState())
            && !$this->dissertationAcl->canDelete($dissertation)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveDissertation($dissertation);

        if ($newDissertation) {
            $this->dissertationAcl->setDefaultAcl($dissertation);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findDissertationById($id)
    {
        $dissertation = $this->realManager->findDissertationById($id);

        if (null !== $dissertation && !$this->dissertationAcl->canView($dissertation)) {
            throw new AccessDeniedException();
        }

        return $dissertation;
    }

    /**
     * {@inheritDoc}
     */
    public function createDissertation($id = null)
    {
        return $this->realManager->createDissertation($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewDissertation(DissertationInterface $dissertation)
    {
        return $this->realManager->isNewDissertation($dissertation);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the dissertation have appropriate view permissions.
     *
     * @param  array   $dissertations A comment tree
     * @return boolean
     */
    protected function authorizeViewDissertation(array $dissertations)
    {
        foreach ($dissertations as $dissertation) {
            if (!$this->dissertationAcl->canView($dissertation)) {
                return false;
            }
        }

        return true;
    }
}
