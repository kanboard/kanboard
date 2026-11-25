<?php

namespace PhpOffice\PhpWord\Reader\Word2007;

use DateTime;
use PhpOffice\PhpWord\Element\Comment;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

class Comments extends AbstractPart
{
    /**
     * Collection name comments.
     *
     * @var string
     */
    protected $collection = 'comments';

    /**
     * Read settings.xml.
     */
    public function read(PhpWord $phpWord): void
    {
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);

        $comments = $phpWord->getComments();

        $nodes = $xmlReader->getElements('*');

        foreach ($nodes as $node) {
            $name = str_replace('w:', '', $node->nodeName);

            $author = $xmlReader->getAttribute('w:author', $node);
            $date = $xmlReader->getAttribute('w:date', $node);
            $initials = $xmlReader->getAttribute('w:initials', $node);

            $element = new Comment($author, new DateTime($date), $initials);

            $range = $this->getCommentReference($xmlReader->getAttribute('w:id', $node));
            if ($range['start']) {
                $range['start']->setCommentRangeStart($element);
            }
            if ($range['end']) {
                $range['end']->setCommentRangeEnd($element);
            }

            $pNodes = $xmlReader->getElements('w:p/w:r', $node);
            foreach ($pNodes as $pNode) {
                $this->readRun($xmlReader, $pNode, $element, $this->collection);
            }

            $phpWord->getComments()->addItem($element);
        }
    }
}
