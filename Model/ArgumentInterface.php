<?php

namespace N1c0\DissertationBundle\Model;

Interface ArgumentInterface
{
    const STATE_VISIBLE = 0;

    const STATE_DELETED = 1;

    const STATE_SPAM = 2;

    const STATE_PENDING = 3;

    /**
     * @return mixed unique ID for this argument
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

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return PartInterface
     */
    public function getPart();

    /**
     * @param PartInterface $part
     */
    public function setPart(PartInterface $part);
}
