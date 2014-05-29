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

        $raw = $this->appDissertation->findDissertationById($id)->getBody();
        
        if('pdf' == $format || 'beamer' == $format) {
            $raw = htmlspecialchars(htmlentities($raw));
            $quotes = array('&amp;quot;', '&amp;laquo;', '&amp;raquo;');
            $raw = str_replace($quotes, '"', $raw);
        }

        $options = array(
            "from"  => "markdown",
            "to"    => $format
        );
        $result = $pandoc->runWith($raw, $options);
        
        return $result;  
    }
}
