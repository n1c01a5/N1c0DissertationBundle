<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadIntroduction 
{
    private $appIntroduction;

    public function __construct($appIntroduction)
    {
        $this->appIntroduction = $appIntroduction;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        if('pdf' == $format || 'beamer' == $format) {
            $raw = htmlspecialchars(htmlentities($raw));
            $quotes = array('&amp;quot;', '&amp;laquo;', '&amp;raquo;');
            $raw = str_replace($quotes, '"', $raw);
        }

        $raw = $this->appIntroduction->findIntroductionById($id)->getBody();
        $options = array(
            "from"  => "markdown",
            "to"    => $format
        );
        $result = $pandoc->runWith($raw, $options);
        
        return $result;  
    }
}
