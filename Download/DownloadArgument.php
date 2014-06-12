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

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
