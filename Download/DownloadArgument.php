<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadArgument 
{
    private $appArgument;

    public function __construct($appArgument)
    {
        $this->appArgument = $appArgument;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();
        // catch the entity
        $raw = $this->appArgument->findArgumentById($id)->getBody();
        $options = array(
            "from"  => "markdown",
            "to"    => $format
        );
        $result = $pandoc->runWith($raw, $options);
        
        return $result;  
    }
}
