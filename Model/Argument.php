<?php

namespace N1c0\DissertationBundle\Model;

use DateTime;

/**
 * Storage agnostic argument dissertation object
 */
abstract class Argument implements ArgumentInterface
{
    /**
     *Argument id
     *
     * @var mixed
     */
    protected $id;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Body
     *
     * @var string
     */
    protected $body;

    /**
     * Should be mapped by the end developer.
     *
     * @var PartInterface
     */
    protected $part;

    /**
     * CommitTitle
     *
     * @var string
     */
    protected $commitTitle;

    /**
     * CommitBody
     *
     * @var string
     */
    protected $commitBody;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Current state of the argument.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * The previous state of the argument.
     *
     * @var integer
     */
    protected $previousState = 0;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param  string
     * @return null
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param  string
     * @return null
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return PartInterface
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @param PartInterface $part
     *
     * @return void
     */
    public function setPart(PartInterface $part)
    {
        $this->part = $part;
    }

    /**
     * @return string
     */
    public function getCommitBody()
    {
        return $this->commitBody;
    }

    /**
     * @param  string
     * @return null
     */
    public function setCommitBody($commitBody)
    {
        $this->commitBody = $commitBody;
    }

    /**
     * @return string
     */
    public function getCommitTitle()
    {
        return $this->commitTitle;
    }

    /**
     * @param  string
     * @return null
     */
    public function setCommitTitle($commitTitle)
    {
        $this->commitTitle = $commitTitle;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return array with the names of the argument authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }

    /**
     * @return array with the name of the argument author
     */
    public function getAuthorName()
    {
        return 'Anonymous';
    }

    /**
     * {@inheritDoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritDoc}
     */
    public function setState($state)
    {
        $this->previousState = $this->state;
        $this->state = $state;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreviousState()
    {
        return $this->previousState;
    }

    public function __toString()
    {
        return 'Argument #'.$this->getId();
    }
}
