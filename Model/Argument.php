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
     * @return array with the names of the argument authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }
}
