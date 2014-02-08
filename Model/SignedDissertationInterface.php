<?php

namespace N1c0\DissertationBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A signed dissertation is bound to a FOS\UserBundle User model.
 */
interface SignedDissertationInterface extends DissertationInterface
{
    /**
     * Sets the author of the Dissertation
     *
     * @param UserInterface $user
     */
    public function setAuthor(UserInterface $author);

    /**
     * Gets the author of the Dissertation
     *
     * @return UserInterface
     */
    public function getAuthor();
}

