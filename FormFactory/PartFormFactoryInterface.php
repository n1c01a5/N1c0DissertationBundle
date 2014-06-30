<?php

namespace N1c0\DissertationBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Part form creator
 */
interface PartFormFactoryInterface
{
    /**
     * Creates a part form
     *
     * @return FormInterface
     */
    public function createForm();
}
