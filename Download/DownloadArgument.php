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

        $raw = $this->appArgument->findArgumentById($id)->getBody();

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
