<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\ArgumentInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface ArgumentAclInterface
{
    /**
     * Checks if the user should be able to create a argument.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canView(ArgumentInterface $argument);

    /**
     * Checks if the user can reply to the supplied 'parent' argument
     * or if not supplied, just the ability to reply.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canReply(ArgumentInterface $parent = null);

    /**
     * Checks if the user should be able to edit a argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canEdit(ArgumentInterface $argument);

    /**
     * Checks if the user should be able to delete a argument.
     *
     * @param  ArgumentInterface $argument
     * @return boolean
     */
    public function canDelete(ArgumentInterface $argument);

    /**
     * Sets the default Acl permissions on a argument.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new ArgumentInterface instances.
     *
     * @param  ArgumentInterface $argument
     * @return void
     */
    public function setDefaultAcl(ArgumentInterface $argument);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    public function installFallbackAcl();

    /**
     * Removes default Acl entries
     * @return void
     */
    public function uninstallFallbackAcl();
}
