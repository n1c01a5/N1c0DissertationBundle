<?php

namespace N1c0\DissertationBundle\Model;

use DateTime;

/**
 * Storage agnostic element dissertation object
 */
abstract class Dissertation implements DissertationInterface
{
    /**
     * Id, a unique string that binds the elements together in a dissertation (tree).
     * It can be a url or really anything unique.
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
     * Current state of the dissertation.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * The previous state of the dissertation.
     *
     * @var integer
     */
    protected $previousState = 0;

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
     * @return array with the names of the dissertation authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }

    public function __toString()
    {
        return 'Element dissertation #'.$this->getId();
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
}
