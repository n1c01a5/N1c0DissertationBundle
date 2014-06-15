<?php

namespace N1c0\DissertationBundle\Model;

Interface DissertationInterface
{
    const STATE_VISIBLE = 0;

    const STATE_DELETED = 1;

    const STATE_SPAM = 2;

    const STATE_PENDING = 3;

    /**
     * @return mixed unique ID for this dissertation
     */
    public function getId();

    /**
     * @return array with authors of the dissertation
     */
    public function getAuthorsName();

    /**
     * @return array with the last author of the dissertation
     */
    public function getAuthorName();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
    
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

    /**
     * Set commitTitle
     *
     * @param string $commitTitle
     * @return DissertationInterface
     */
    public function setCommitTitle($commitTitle);

    /**
     * Get commitTitle
     *
     * @return string 
     */
    public function getCommitTitle();

    /**
     * Set commitBody
     *
     * @param string $commitBody
     * @return DissertationInterface
     */
    public function setCommitBody($commitBody);

    /**
     * Get commitBody
     *
     * @return string 
     */
    public function getCommitBody();

    /**
     * @return integer The current state of the comment
     */
    public function getState();

    /**
     * @param integer state
     */
    public function setState($state);

    /**
     * Gets the previous state.
     *
     * @return integer
     */
    public function getPreviousState();
}
