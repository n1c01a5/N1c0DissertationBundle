<?php

namespace N1c0\DissertationBundle\Model;

Interface ArgumentInterface
{
    /**
     * @return mixed unique ID for this argument
     */
    public function getId();
    
    /**
     * Set title
     *
     * @param string $title
     * @return ArgumentInterface
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
     * @return ArgumentInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();
}