<?php

namespace N1c0\DissertationBundle\Model;

Interface PartInterface
{
    /**
     * @return mixed unique ID for this part
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
     * @return PartInterface
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
     * @return PartInterface
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