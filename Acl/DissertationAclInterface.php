<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\DissertationInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface DissertationAclInterface
{
    /**
     * Checks if the user should be able to create a dissertation.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canView(DissertationInterface $dissertation);

    /**
     * Checks if the user can reply to the supplied 'parent' dissertation
     * or if not supplied, just the ability to reply.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canReply(DissertationInterface $parent = null);

    /**
     * Checks if the user should be able to edit a dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canEdit(DissertationInterface $dissertation);

    /**
     * Checks if the user should be able to delete a dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return boolean
     */
    public function canDelete(DissertationInterface $dissertation);

    /**
     * Sets the default Acl permissions on a dissertation.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new DissertationInterface instances.
     *
     * @param  DissertationInterface $dissertation
     * @return void
     */
    public function setDefaultAcl(DissertationInterface $dissertation);

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
