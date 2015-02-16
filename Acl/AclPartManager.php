<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\PartInterface;
use N1c0\DissertationBundle\Model\PartManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of PartManagerInterface and
 * performs Acl checks with the configured Part Acl service.
 */
class AclPartManager implements PartManagerInterface
{
    /**
     * The PartManager instance to be wrapped with ACL.
     *
     * @var PartManagerInterface
     */
    protected $realManager;

    /**
     * The PartAcl instance for checking permissions.
     *
     * @var PartAclInterface
     */
    protected $partAcl;

    /**
     * Constructor.
     *
     * @param PartManagerInterface $partManager The concrete PartManager service
     * @param PartAclInterface     $partAcl     The Part Acl service
     */
    public function __construct(PartManagerInterface $partManager, PartAclInterface $partAcl)
    {
        $this->realManager = $partManager;
        $this->partAcl  = $partAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit, $offset)
    {
        $parts = $this->realManager->all($limit, $offset);

        if (!$this->authorizeViewPart($parts)) {
            throw new AccessDeniedException();
        }

        return $parts;
    }

    /**
     * {@inheritDoc}
     */
    public function findPartBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findPartsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllParts(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function savePart(PartInterface $part)
    {
        if (!$this->partAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newPart = $this->isNewPart($part);

        if (!$newPart && !$this->partAcl->canEdit($part)) {
            throw new AccessDeniedException();
        }

        if (($part::STATE_DELETED === $part->getState() || $part::STATE_DELETED === $part->getPreviousState())
            && !$this->partAcl->canDelete($part)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->savePart($part);

        if ($newPart) {
            $this->partAcl->setDefaultAcl($part);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findPartById($id)
    {
        $part = $this->realManager->findPartById($id);

        if (null !== $part && !$this->partAcl->canView($part)) {
            throw new AccessDeniedException();
        }

        return $part;
    }

    /**
     * {@inheritDoc}
     */
    public function createPart($id = null)
    {
        return $this->realManager->createPart($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewPart(PartInterface $part)
    {
        return $this->realManager->isNewPart($part);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the part have appropriate view permissions.
     *
     * @param  array   $parts A comment tree
     * @return boolean
     */
    protected function authorizeViewPart(array $parts)
    {
        foreach ($parts as $part) {
            if (!$this->partAcl->canView($part)) {
                return false;
            }
        }

        return true;
    }
}
