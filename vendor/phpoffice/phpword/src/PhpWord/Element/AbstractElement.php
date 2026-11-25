<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Element;

use DateTime;
use InvalidArgumentException;
use PhpOffice\PhpWord\Collection\Comments;
use PhpOffice\PhpWord\Media;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style;

/**
 * Element abstract class.
 *
 * @since 0.10.0
 */
abstract class AbstractElement
{
    /**
     * PhpWord object.
     *
     * @var ?PhpWord
     */
    protected $phpWord;

    /**
     * Section Id.
     *
     * @var int
     */
    protected $sectionId;

    /**
     * Document part type: Section|Header|Footer|Footnote|Endnote.
     *
     * Used by textrun and cell container to determine where the element is
     * located because it will affect the availability of other element,
     * e.g. footnote will not be available when $docPart is header or footer.
     *
     * @var string
     */
    protected $docPart = 'Section';

    /**
     * Document part Id.
     *
     * For header and footer, this will be = ($sectionId - 1) * 3 + $index
     * because the max number of header/footer in every page is 3, i.e.
     * AUTO, FIRST, and EVEN (AUTO = ODD)
     *
     * @var int
     */
    protected $docPartId = 1;

    /**
     * Index of element in the elements collection (start with 1).
     *
     * @var int
     */
    protected $elementIndex = 1;

    /**
     * Unique Id for element.
     *
     * @var string
     */
    protected $elementId;

    /**
     * Relation Id.
     *
     * @var int
     */
    protected $relationId;

    /**
     * Depth of table container nested level; Primarily used for RTF writer/reader.
     *
     * 0 = Not in a table; 1 = in a table; 2 = in a table inside another table, etc.
     *
     * @var int
     */
    private $nestedLevel = 0;

    /**
     * A reference to the parent.
     *
     * @var null|AbstractElement
     */
    private $parent;

    /**
     * changed element info.
     *
     * @var TrackChange
     */
    private $trackChange;

    /**
     * Parent container type.
     *
     * @var string
     */
    private $parentContainer;

    /**
     * Has media relation flag; true for Link, Image, and Object.
     *
     * @var bool
     */
    protected $mediaRelation = false;

    /**
     * Is part of collection; true for Title, Footnote, Endnote, Chart, and Comment.
     *
     * @var bool
     */
    protected $collectionRelation = false;

    /**
     * The start position for the linked comments.
     *
     * @var Comments
     */
    protected $commentsRangeStart;

    /**
     * The end position for the linked comments.
     *
     * @var Comments
     */
    protected $commentsRangeEnd;

    /**
     * Get PhpWord.
     */
    public function getPhpWord(): ?PhpWord
    {
        return $this->phpWord;
    }

    /**
     * Set PhpWord as reference.
     */
    public function setPhpWord(?PhpWord $phpWord = null): void
    {
        $this->phpWord = $phpWord;
    }

    /**
     * Get section number.
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set doc part.
     *
     * @param string $docPart
     * @param int $docPartId
     */
    public function setDocPart($docPart, $docPartId = 1): void
    {
        $this->docPart = $docPart;
        $this->docPartId = $docPartId;
    }

    /**
     * Get doc part.
     *
     * @return string
     */
    public function getDocPart()
    {
        return $this->docPart;
    }

    /**
     * Get doc part Id.
     *
     * @return int
     */
    public function getDocPartId()
    {
        return $this->docPartId;
    }

    /**
     * Return media element (image, object, link) container name.
     *
     * @return string section|headerx|footerx|footnote|endnote
     */
    private function getMediaPart()
    {
        $mediaPart = $this->docPart;
        if ($mediaPart == 'Header' || $mediaPart == 'Footer') {
            $mediaPart .= $this->docPartId;
        }

        return strtolower($mediaPart);
    }

    /**
     * Get element index.
     *
     * @return int
     */
    public function getElementIndex()
    {
        return $this->elementIndex;
    }

    /**
     * Set element index.
     *
     * @param int $value
     */
    public function setElementIndex($value): void
    {
        $this->elementIndex = $value;
    }

    /**
     * Get element unique ID.
     *
     * @return string
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Set element unique ID from 6 first digit of md5.
     */
    public function setElementId(): void
    {
        $this->elementId = substr(md5((string) mt_rand()), 0, 6);
    }

    /**
     * Get relation Id.
     *
     * @return int
     */
    public function getRelationId()
    {
        return $this->relationId;
    }

    /**
     * Set relation Id.
     *
     * @param int $value
     */
    public function setRelationId($value): void
    {
        $this->relationId = $value;
    }

    /**
     * Get nested level.
     *
     * @return int
     */
    public function getNestedLevel()
    {
        return $this->nestedLevel;
    }

    /**
     * Get comments start.
     */
    public function getCommentsRangeStart(): ?Comments
    {
        return $this->commentsRangeStart;
    }

    /**
     * Get comment start.
     */
    public function getCommentRangeStart(): ?Comment
    {
        if ($this->commentsRangeStart != null) {
            return $this->commentsRangeStart->getItem($this->commentsRangeStart->countItems());
        }

        return null;
    }

    /**
     * Set comment start.
     */
    public function setCommentRangeStart(Comment $value): void
    {
        if ($this instanceof Comment) {
            throw new InvalidArgumentException('Cannot set a Comment on a Comment');
        }
        if ($this->commentsRangeStart == null) {
            $this->commentsRangeStart = new Comments();
        }
        // Set ID early to avoid duplicates.
        if ($value->getElementId() == null) {
            $value->setElementId();
        }
        foreach ($this->commentsRangeStart->getItems() as $comment) {
            if ($value->getElementId() == $comment->getElementId()) {
                return;
            }
        }
        $idxItem = $this->commentsRangeStart->addItem($value);
        $this->commentsRangeStart->getItem($idxItem)->setStartElement($this);
    }

    /**
     * Get comments end.
     */
    public function getCommentsRangeEnd(): ?Comments
    {
        return $this->commentsRangeEnd;
    }

    /**
     * Get comment end.
     */
    public function getCommentRangeEnd(): ?Comment
    {
        if ($this->commentsRangeEnd != null) {
            return $this->commentsRangeEnd->getItem($this->commentsRangeEnd->countItems());
        }

        return null;
    }

    /**
     * Set comment end.
     */
    public function setCommentRangeEnd(Comment $value): void
    {
        if ($this instanceof Comment) {
            throw new InvalidArgumentException('Cannot set a Comment on a Comment');
        }
        if ($this->commentsRangeEnd == null) {
            $this->commentsRangeEnd = new Comments();
        }
        // Set ID early to avoid duplicates.
        if ($value->getElementId() == null) {
            $value->setElementId();
        }
        foreach ($this->commentsRangeEnd->getItems() as $comment) {
            if ($value->getElementId() == $comment->getElementId()) {
                return;
            }
        }
        $idxItem = $this->commentsRangeEnd->addItem($value);
        $this->commentsRangeEnd->getItem($idxItem)->setEndElement($this);
    }

    /**
     * Get parent element.
     *
     * @return null|AbstractElement
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent container.
     *
     * Passed parameter should be a container, except for Table (contain Row) and Row (contain Cell)
     */
    public function setParentContainer(self $container): void
    {
        $this->parentContainer = substr(get_class($container), strrpos(get_class($container), '\\') + 1);
        $this->parent = $container;

        // Set nested level
        $this->nestedLevel = $container->getNestedLevel();
        if ($this->parentContainer == 'Cell') {
            ++$this->nestedLevel;
        }

        // Set phpword
        $this->setPhpWord($container->getPhpWord());

        // Set doc part
        if (!$this instanceof Footnote) {
            $this->setDocPart($container->getDocPart(), $container->getDocPartId());
        }

        $this->setMediaRelation();
        $this->setCollectionRelation();
    }

    /**
     * Set relation Id for media elements (link, image, object; legacy of OOXML).
     *
     * - Image element needs to be passed to Media object
     * - Icon needs to be set for Object element
     */
    private function setMediaRelation(): void
    {
        if (!$this instanceof Link && !$this instanceof Image && !$this instanceof OLEObject) {
            return;
        }

        $elementName = substr(static::class, strrpos(static::class, '\\') + 1);
        if ($elementName == 'OLEObject') {
            $elementName = 'Object';
        }
        $mediaPart = $this->getMediaPart();
        $source = $this->getSource();
        $image = null;
        if ($this instanceof Image) {
            $image = $this;
        }
        $rId = Media::addElement($mediaPart, strtolower($elementName), $source, $image);
        $this->setRelationId($rId);

        if ($this instanceof OLEObject) {
            $icon = $this->getIcon();
            $rId = Media::addElement($mediaPart, 'image', $icon, new Image($icon));
            $this->setImageRelationId($rId);
        }
    }

    /**
     * Set relation Id for elements that will be registered in the Collection subnamespaces.
     */
    private function setCollectionRelation(): void
    {
        if ($this->collectionRelation === true && $this->phpWord instanceof PhpWord) {
            $elementName = substr(static::class, strrpos(static::class, '\\') + 1);
            $addMethod = "add{$elementName}";
            $rId = $this->phpWord->$addMethod($this);
            $this->setRelationId($rId);
        }
    }

    /**
     * Check if element is located in Section doc part (as opposed to Header/Footer).
     *
     * @return bool
     */
    public function isInSection()
    {
        return $this->docPart == 'Section';
    }

    /**
     * Set new style value.
     *
     * @param mixed $styleObject Style object
     * @param null|array|string|Style $styleValue Style value
     * @param bool $returnObject Always return object
     *
     * @return mixed
     */
    protected function setNewStyle($styleObject, $styleValue = null, $returnObject = false)
    {
        if (null !== $styleValue && is_array($styleValue)) {
            $styleObject->setStyleByArray($styleValue);
            $style = $styleObject;
        } else {
            $style = $returnObject ? $styleObject : $styleValue;
        }

        return $style;
    }

    /**
     * Sets the trackChange information.
     */
    public function setTrackChange(TrackChange $trackChange): void
    {
        $this->trackChange = $trackChange;
    }

    /**
     * Gets the trackChange information.
     *
     * @return TrackChange
     */
    public function getTrackChange()
    {
        return $this->trackChange;
    }

    /**
     * Set changed.
     *
     * @param string $type INSERTED|DELETED
     * @param string $author
     * @param null|DateTime|int $date allways in UTC
     */
    public function setChangeInfo($type, $author, $date = null): void
    {
        $this->trackChange = new TrackChange($type, $author, $date);
    }

    /**
     * Set enum value.
     *
     * @param null|string $value
     * @param string[] $enum
     * @param null|string $default
     *
     * @return null|string
     *
     * @todo Merge with the same method in AbstractStyle
     */
    protected function setEnumVal($value = null, $enum = [], $default = null)
    {
        if ($value !== null && trim($value) != '' && !empty($enum) && !in_array($value, $enum)) {
            throw new InvalidArgumentException("Invalid style value: {$value}");
        } elseif ($value === null || trim($value) == '') {
            $value = $default;
        }

        return $value;
    }
}
