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

        $dissertation = $this->appDissertation->findDissertationById($id);

        $raw = '#'.$dissertation->getTitle();
        $raw .= "\r\n";
        $raw .= '##'.$dissertation->getBody();
        
        $lenghtElement = max(count($dissertation->getIntroductions()), count($dissertation->getArguments()));

        for($i = 0; $i < $lenghtElement; $i++) {
            $raw .= "\r\n";
            $raw .= $dissertation->getIntroductions()[$i]->getBody();
            $raw .= "\r\n";
            $raw .= $dissertation->getArguments()[$i]->getBody();
        }

        $options = array(
            "latex-engine" => "xelatex",
            "from"  => "markdown",
            "to"    => $format,
            "toc" => null
        );

        return $pandoc->runWith($raw, $options);
    }
}
