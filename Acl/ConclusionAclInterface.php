<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\ConclusionInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface ConclusionAclInterface
{
    /**
     * Checks if the user should be able to create a conclusion.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canView(ConclusionInterface $conclusion);

    /**
     * Checks if the user can reply to the supplied 'parent' conclusion
     * or if not supplied, just the ability to reply.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canReply(ConclusionInterface $parent = null);

    /**
     * Checks if the user should be able to edit a conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canEdit(ConclusionInterface $conclusion);

    /**
     * Checks if the user should be able to delete a conclusion.
     *
     * @param  ConclusionInterface $conclusion
     * @return boolean
     */
    public function canDelete(ConclusionInterface $conclusion);

    /**
     * Sets the default Acl permissions on a conclusion.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new ConclusionInterface instances.
     *
     * @param  ConclusionInterface $conclusion
     * @return void
     */
    public function setDefaultAcl(ConclusionInterface $conclusion);

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
