<?php

namespace N1c0\DissertationBundle\Model;

Interface TransitionInterface
{
    /**
     * @return mixed unique ID for this transition
     */
    public function getId();

    /**
     * @return array with authors of the dissertation
     */
    public function getAuthorsName();
    
    /**
     * Set title
     *
     * @param string $title
     * @return TransitionInterface
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
     * @return TransitionInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return DissertationInterface
     */
    public function getDissertation();

    /**
     * @param DissertationInterface $dissertation
     */
    public function setDissertation(DissertationInterface $dissertation);
}
