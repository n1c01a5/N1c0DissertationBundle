<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadTransition 
{
    private $appTransition;

    public function __construct($appTransition)
    {
        $this->appTransition = $appTransition;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        $transition = $this->appTransition->findTransitionById($id);

        $raw = '% efez'.$transition->getTitle(); 
        $raw .= "\r\n";
        $raw .= '%'; 

        foreach($transition->getAuthors() as $author) {
            $raw .= $author.' ;';
        }

        $raw .= "\r\n";
        $raw .= '%'.$transition->getCreatedAt()->format("m M Y");      
        $raw .= "\r\n";
        $raw .= $transition->getBody();


        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
