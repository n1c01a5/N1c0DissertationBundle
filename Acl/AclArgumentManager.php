<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\PartInterface;
use N1c0\DissertationBundle\Model\ArgumentInterface;
use N1c0\DissertationBundle\Model\ArgumentManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of ArgumentManagerInterface and
 * performs Acl checks with the configured Argument Acl service.
 */
class AclArgumentManager implements ArgumentManagerInterface
{
    /**
     * The ArgumentManager instance to be wrapped with ACL.
     *
     * @var ArgumentManagerInterface
     */
    protected $realManager;

    /**
     * The ArgumentAcl instance for checking permissions.
     *
     * @var ArgumentAclInterface
     */
    protected $argumentAcl;

    /**
     * Constructor.
     *
     * @param ArgumentManagerInterface $argumentManager The concrete ArgumentManager service
     * @param ArgumentAclInterface     $argumentAcl     The Argument Acl service
     */
    public function __construct(ArgumentManagerInterface $argumentManager, ArgumentAclInterface $argumentAcl)
    {
        $this->realManager = $argumentManager;
        $this->argumentAcl  = $argumentAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit, $offset)
    {
        $arguments = $this->realManager->all($limit, $offset);

        if (!$this->authorizeViewArgument($arguments)) {
            throw new AccessDeniedException();
        }

        return $arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function findArgumentBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findArgumentsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllArguments(){
    }


    /**
     * {@inheritDoc}
     */
    public function saveArgument(ArgumentInterface $argument)
    {
        if (!$this->argumentAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newArgument = $this->isNewArgument($argument);

        if (!$newArgument && !$this->argumentAcl->canEdit($argument)) {
            throw new AccessDeniedException();
        }

        if (($argument::STATE_DELETED === $argument->getState() || $argument::STATE_DELETED === $argument->getPreviousState())
            && !$this->argumentAcl->canDelete($argument)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveArgument($argument);

        if ($newArgument) {
            $this->argumentAcl->setDefaultAcl($argument);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findArgumentById($id)
    {
        $argument = $this->realManager->findArgumentById($id);

        if (null !== $argument && !$this->argumentAcl->canView($argument)) {
            throw new AccessDeniedException();
        }

        return $argument;
    }

    /**
     * {@inheritDoc}
     */
    public function createArgument(PartInterface $part)
    {
        return $this->realManager->createArgument($part);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewArgument(ArgumentInterface $argument)
    {
        return $this->realManager->isNewArgument($argument);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the argument have appropriate view permissions.
     *
     * @param  array   $arguments A comment tree
     * @return boolean
     */
    protected function authorizeViewArgument(array $arguments)
    {
        foreach ($arguments as $argument) {
            if (!$this->argumentAcl->canView($argument)) {
                return false;
            }
        }

        return true;
    }
}
