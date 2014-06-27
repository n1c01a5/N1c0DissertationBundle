<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadConclusion 
{
    private $appConclusion;

    public function __construct($appConclusion)
    {
        $this->appConclusion = $appConclusion;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        $conclusion = $this->appConclusion->findConclusionById($id);

        $raw = '%'.$conclusion->getTitle(); 
        $raw .= "\r\n";
        $raw .= '%';

        foreach($conclusion->getAuthors() as $author) {
            $raw .= $author.' ;';
        }

        $raw .= "\r\n";
        $raw .= '%'.$conclusion->getCreatedAt()->format("m M Y");      
        $raw .= "\r\n";
        $raw .= $conclusion->getBody();


        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
