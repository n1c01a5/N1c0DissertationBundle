<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\IntroductionInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface IntroductionAclInterface
{
    /**
     * Checks if the user should be able to create a introduction.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canView(IntroductionInterface $introduction);

    /**
     * Checks if the user can reply to the supplied 'parent' introduction
     * or if not supplied, just the ability to reply.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canReply(IntroductionInterface $parent = null);

    /**
     * Checks if the user should be able to edit a introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canEdit(IntroductionInterface $introduction);

    /**
     * Checks if the user should be able to delete a introduction.
     *
     * @param  IntroductionInterface $introduction
     * @return boolean
     */
    public function canDelete(IntroductionInterface $introduction);

    /**
     * Sets the default Acl permissions on a introduction.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new IntroductionInterface instances.
     *
     * @param  IntroductionInterface $introduction
     * @return void
     */
    public function setDefaultAcl(IntroductionInterface $introduction);

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
