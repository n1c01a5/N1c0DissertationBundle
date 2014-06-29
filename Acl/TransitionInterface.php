<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TransitionInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface TransitionAclInterface
{
    /**
     * Checks if the user should be able to create a transition.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canView(TransitionInterface $transition);

    /**
     * Checks if the user can reply to the supplied 'parent' transition
     * or if not supplied, just the ability to reply.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canReply(TransitionInterface $parent = null);

    /**
     * Checks if the user should be able to edit a transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canEdit(TransitionInterface $transition);

    /**
     * Checks if the user should be able to delete a transition.
     *
     * @param  TransitionInterface $transition
     * @return boolean
     */
    public function canDelete(TransitionInterface $transition);

    /**
     * Sets the default Acl permissions on a transition.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new TransitionInterface instances.
     *
     * @param  TransitionInterface $transition
     * @return void
     */
    public function setDefaultAcl(TransitionInterface $transition);

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
