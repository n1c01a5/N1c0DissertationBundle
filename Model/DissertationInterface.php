<?php

namespace N1c0\DissertationBundle\Model;

Interface DissertationInterface
{
    /**
     * Set title
     *
     * @param string $title
     * @return DissertationInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     * @return DissertationInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();
}
