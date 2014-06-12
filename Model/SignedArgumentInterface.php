<?php

namespace N1c0\DissertationBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A signed argument is bound to a FOS\UserBundle User model.
 */
interface SignedArgumentInterface extends ArgumentInterface
{
    /**
     * Add user 
     *
     * @param Application\UserBundle\Entity\User $user
     */
    public function addAuthor(\Application\UserBundle\Entity\User $user);

    /**
     * Remove user
     *
     * @param Application\UserBundle\Entity\User $user
     */
    public function removeUser(\Application\UserBundle\Entity\User $user);

    /**
     * Gets the authors of the Argument
     *
     * @return UserInterface
     */
    public function getAuthors();
}

