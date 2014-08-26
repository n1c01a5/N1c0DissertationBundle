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

        $raw = '%'.$dissertation->getTitle(); 
        $raw .= "\r\n";

        foreach($dissertation->getAuthors() as $author) {
            $raw .= $author.' ;';
        }

        $raw .= "\r\n";
        $raw .= "# Sujet de la dissertation";
        $raw .= "\r\n";
        $raw .= $dissertation->getBody();


        $lenghtElement = count($dissertation->getIntroductions());

        for($i = 0; $i < $lenghtElement; $i++) {
            $raw .= "\r\n";
            $raw .= "\r\n";
            $introduction = $dissertation->getIntroductions()[$i];
            $raw .= '##'.$introduction->getTitle();
            $raw .= "\r\n";
            $raw .= $introduction->getBody();
        }
        
        $parts = $dissertation->getParts();
        $lenghtPart = count($parts);

        for($i = 0; $i < $lenghtPart; $i++) {
            $arguments = $parts[$i]->getArguments();
            $lenghtArgument = count($arguments);
            for($j = 0; $j < $lenghtArgument; $j++) {
                $raw .= "\r\n";
                $raw .= "\r\n";
                $raw .= '##'.$arguments[$j]->getTitle();
                $raw .= "\r\n";
                $raw .= $arguments[$j]->getBody();
            }
        }

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format,
            "toc"          => null
        );

        return $pandoc->runWith($raw, $options);
    }
}
