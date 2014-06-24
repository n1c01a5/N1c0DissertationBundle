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

        $introduction = $this->appIntroduction->findIntroductionById($id);

        $raw = '#'.$introduction->getTitle();
        $raw .= "\r\n";
        $raw .= '##'.$introduction->getBody();

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
