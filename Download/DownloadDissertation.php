<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadDissertation 
{
    private $appDissertation;

    public function __construct($appDissertation)
    {
        $this->appDissertation = $appDissertation;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();
        // catch the entity
        $raw = $this->appDissertation->findDissertationById($id)->getBody();
        $options = array(
            "from"  => "markdown",
            "to"    => $format
        );
        $result = $pandoc->runWith($raw, $options);
        
        return $result;  
    }
}
