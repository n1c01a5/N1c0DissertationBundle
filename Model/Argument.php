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
     * @var DissertationInterface
     */
    protected $dissertation;

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
     * @return DissertationInterface
     */
    public function getDissertation()
    {
        return $this->dissertation;
    }

    /**
     * @param DissertationInterface $dissertation
     *
     * @return void
     */
    public function setDissertation(DissertationInterface $dissertation)
    {
        $this->dissertation = $dissertation;
    }

    public function __toString()
    {
        return 'Argument #'.$this->getId();
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
     * @return array with the names of the argument authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }
}
