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

        $argument = $this->appArgument->findArgumentById($id);

        $raw = '#'.$argument->getTitle();
        $raw .= "\r\n";
        $raw .= '##'.$argument->getBody();

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
