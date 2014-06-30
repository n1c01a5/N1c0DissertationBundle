<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadPart 
{
    private $appPart;

    public function __construct($appPart)
    {
        $this->appPart = $appPart;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        $part = $this->appPart->findPartById($id);

        $raw = '% efez'.$part->getTitle(); 
        $raw .= "\r\n";
        $raw .= '%'; 

        foreach($part->getAuthors() as $author) {
            $raw .= $author.' ;';
        }

        $raw .= "\r\n";
        $raw .= '%'.$part->getCreatedAt()->format("m M Y");      
        $raw .= "\r\n";
        $raw .= $part->getBody();


        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
