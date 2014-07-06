<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\PartInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface PartAclInterface
{
    /**
     * Checks if the user should be able to create a part.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canView(PartInterface $part);

    /**
     * Checks if the user should be able to edit a part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canEdit(PartInterface $part);

    /**
     * Checks if the user should be able to delete a part.
     *
     * @param  PartInterface $part
     * @return boolean
     */
    public function canDelete(PartInterface $part);

    /**
     * Sets the default Acl permissions on a part.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new PartInterface instances.
     *
     * @param  PartInterface $part
     * @return void
     */
    public function setDefaultAcl(PartInterface $part);

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
