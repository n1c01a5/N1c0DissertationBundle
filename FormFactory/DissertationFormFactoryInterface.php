<?php

namespace N1c0\DissertationBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Dissertation form creator
 */
interface DissertationFormFactoryInterface
{
    /**
     * Creates a dissertation form
     *
     * @return FormInterface
     */
    public function createForm();
}
