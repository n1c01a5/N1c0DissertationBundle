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
        $raw .= '%'.$dissertation->getCreatedAt()->format("m M Y");      
        $raw .= "\r\n";
        $raw .= "# Sujet de la dissertation";
        $raw .= "\r\n";
        $raw .= $dissertation->getBody();


        $lenghtElement = count($dissertation->getIntroductions());

        for($i = 0; $i < $lenghtElement; $i++) {
            $raw .= "\r\n";
            $raw .= "\r\n";
            $raw .= '##'.$dissertation->getIntroductions()[$i]->getTitle();
            $raw .= "\r\n";
            $raw .= $dissertation->getIntroductions()[$i]->getBody();
        }
        
        $lenghtElement = count($dissertation->getArguments());

        for($i = 0; $i < $lenghtElement; $i++) {
            $raw .= "\r\n";
            $raw .= "\r\n";
            $raw .= '##'.$dissertation->getArguments()[$i]->getTitle();
            $raw .= "\r\n";
            $raw .= $dissertation->getArguments()[$i]->getBody();
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
