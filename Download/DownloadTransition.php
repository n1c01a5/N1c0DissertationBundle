<?php

namespace N1c0\DissertationBundle\Download;

use Pandoc\Pandoc;

class DownloadTrasition 
{
    private $appTrasition;

    public function __construct($appTrasition)
    {
        $this->appTrasition = $appTrasition;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        $trasition = $this->appTrasition->findTrasitionById($id);

        $raw = '%'.$trasition->getTitle(); 
        $raw .= "\r\n";
        $raw .= '%';

        foreach($trasition->getAuthors() as $author) {
            $raw .= $author.' ;';
        }

        $raw .= "\r\n";
        $raw .= '%'.$trasition->getCreatedAt()->format("m M Y");      
        $raw .= "\r\n";
        $raw .= $trasition->getBody();


        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format
        );

        return  $pandoc->runWith($raw, $options);
    }
}
