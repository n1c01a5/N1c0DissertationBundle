<?php

namespace N1c0\DissertationBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Transition form creator
 */
interface TransitionFormFactoryInterface
{
    /**
     * Creates a transition form
     *
     * @return FormInterface
     */
    public function createForm();
}
