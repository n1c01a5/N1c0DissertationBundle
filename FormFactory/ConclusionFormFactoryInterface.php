<?php

namespace N1c0\DissertationBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Conclusion form creator
 */
interface ConclusionFormFactoryInterface
{
    /**
     * Creates a conclusion form
     *
     * @return FormInterface
     */
    public function createForm();
}
