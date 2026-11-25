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

namespace PhpOffice\PhpWord\Metadata;

use DateTime;

/**
 * Document information.
 */
class DocInfo
{
    /** @const string Property type constants */
    const PROPERTY_TYPE_BOOLEAN = 'b';
    const PROPERTY_TYPE_INTEGER = 'i';
    const PROPERTY_TYPE_FLOAT = 'f';
    const PROPERTY_TYPE_DATE = 'd';
    const PROPERTY_TYPE_STRING = 's';
    const PROPERTY_TYPE_UNKNOWN = 'u';

    /**
     * Creator.
     *
     * @var string
     */
    private $creator;

    /**
     * LastModifiedBy.
     *
     * @var string
     */
    private $lastModifiedBy;

    /**
     * Created.
     *
     * @var int
     */
    private $created;

    /**
     * Modified.
     *
     * @var int
     */
    private $modified;

    /**
     * Title.
     *
     * @var string
     */
    private $title;

    /**
     * Description.
     *
     * @var string
     */
    private $description;

    /**
     * Subject.
     *
     * @var string
     */
    private $subject;

    /**
     * Keywords.
     *
     * @var string
     */
    private $keywords;

    /**
     * Category.
     *
     * @var string
     */
    private $category;

    /**
     * Company.
     *
     * @var string
     */
    private $company;

    /**
     * Manager.
     *
     * @var string
     */
    private $manager;

    /**
     * Custom Properties.
     *
     * @var array
     */
    private $customProperties = [];

    /**
     * Create new instance.
     */
    public function __construct()
    {
        $this->creator = '';
        $this->lastModifiedBy = $this->creator;
        $this->created = time();
        $this->modified = time();
        $this->title = '';
        $this->subject = '';
        $this->description = '';
        $this->keywords = '';
        $this->category = '';
        $this->company = '';
        $this->manager = '';
    }

    /**
     * Get Creator.
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set Creator.
     *
     * @param  string $value
     *
     * @return self
     */
    public function setCreator($value = '')
    {
        $this->creator = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Last Modified By.
     *
     * @return string
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set Last Modified By.
     *
     * @param  string $value
     *
     * @return self
     */
    public function setLastModifiedBy($value = '')
    {
        $this->lastModifiedBy = $this->setValue($value, $this->creator);

        return $this;
    }

    /**
     * Get Created.
     *
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Created.
     *
     * @param  int $value
     *
     * @return self
     */
    public function setCreated($value = null)
    {
        $this->created = $this->setValue($value, time());

        return $this;
    }

    /**
     * Get Modified.
     *
     * @return int
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set Modified.
     *
     * @param  int $value
     *
     * @return self
     */
    public function setModified($value = null)
    {
        $this->modified = $this->setValue($value, time());

        return $this;
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @param  string $value
     *
     * @return self
     */
    public function setTitle($value = '')
    {
        $this->title = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description.
     *
     * @param  string $value
     *
     * @return self
     */
    public function setDescription($value = '')
    {
        $this->description = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set Subject.
     *
     * @param  string $value
     *
     * @return self
     */
    public function setSubject($value = '')
    {
        $this->subject = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Keywords.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set Keywords.
     *
     * @param string $value
     *
     * @return self
     */
    public function setKeywords($value = '')
    {
        $this->keywords = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set Category.
     *
     * @param string $value
     *
     * @return self
     */
    public function setCategory($value = '')
    {
        $this->category = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Company.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set Company.
     *
     * @param string $value
     *
     * @return self
     */
    public function setCompany($value = '')
    {
        $this->company = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get Manager.
     *
     * @return string
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set Manager.
     *
     * @param string $value
     *
     * @return self
     */
    public function setManager($value = '')
    {
        $this->manager = $this->setValue($value, '');

        return $this;
    }

    /**
     * Get a List of Custom Property Names.
     *
     * @return array of string
     */
    public function getCustomProperties()
    {
        return array_keys($this->customProperties);
    }

    /**
     * Check if a Custom Property is defined.
     *
     * @param string $propertyName
     *
     * @return bool
     */
    public function isCustomPropertySet($propertyName)
    {
        return isset($this->customProperties[$propertyName]);
    }

    /**
     * Get a Custom Property Value.
     *
     * @param string $propertyName
     *
     * @return mixed
     */
    public function getCustomPropertyValue($propertyName)
    {
        if ($this->isCustomPropertySet($propertyName)) {
            return $this->customProperties[$propertyName]['value'];
        }

        return null;
    }

    /**
     * Get a Custom Property Type.
     *
     * @param string $propertyName
     *
     * @return ?string
     */
    public function getCustomPropertyType($propertyName)
    {
        if ($this->isCustomPropertySet($propertyName)) {
            return $this->customProperties[$propertyName]['type'];
        }

        return null;
    }

    /**
     * Set a Custom Property.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param string $propertyType
     *   'i': Integer
     *   'f': Floating Point
     *   's': String
     *   'd': Date/Time
     *   'b': Boolean
     *
     * @return self
     */
    public function setCustomProperty($propertyName, $propertyValue = '', $propertyType = null)
    {
        $propertyTypes = [
            self::PROPERTY_TYPE_INTEGER,
            self::PROPERTY_TYPE_FLOAT,
            self::PROPERTY_TYPE_STRING,
            self::PROPERTY_TYPE_DATE,
            self::PROPERTY_TYPE_BOOLEAN,
        ];
        if (($propertyType === null) || (!in_array($propertyType, $propertyTypes))) {
            if ($propertyValue === null) {
                $propertyType = self::PROPERTY_TYPE_STRING;
            } elseif (is_float($propertyValue)) {
                $propertyType = self::PROPERTY_TYPE_FLOAT;
            } elseif (is_int($propertyValue)) {
                $propertyType = self::PROPERTY_TYPE_INTEGER;
            } elseif (is_bool($propertyValue)) {
                $propertyType = self::PROPERTY_TYPE_BOOLEAN;
            } elseif ($propertyValue instanceof DateTime) {
                $propertyType = self::PROPERTY_TYPE_DATE;
            } else {
                $propertyType = self::PROPERTY_TYPE_STRING;
            }
        }

        $this->customProperties[$propertyName] = [
            'value' => $propertyValue,
            'type' => $propertyType,
        ];

        return $this;
    }

    /**
     * Convert document property based on type.
     *
     * @param string $propertyValue
     * @param string $propertyType
     *
     * @return mixed
     */
    public static function convertProperty($propertyValue, $propertyType)
    {
        $conversion = self::getConversion($propertyType);

        switch ($conversion) {
            case 'empty': // Empty
                return '';
            case 'null': // Null
                return null;
            case 'int': // Signed integer
                return (int) $propertyValue;
            case 'uint': // Unsigned integer
                return abs((int) $propertyValue);
            case 'float': // Float
                return (float) $propertyValue;
            case 'date': // Date
                return strtotime($propertyValue);
            case 'bool': // Boolean
                return $propertyValue == 'true';
        }

        return $propertyValue;
    }

    /**
     * Convert document property type.
     *
     * @param string $propertyType
     *
     * @return string
     */
    public static function convertPropertyType($propertyType)
    {
        $typeGroups = [
            self::PROPERTY_TYPE_INTEGER => ['i1', 'i2', 'i4', 'i8', 'int', 'ui1', 'ui2', 'ui4', 'ui8', 'uint'],
            self::PROPERTY_TYPE_FLOAT => ['r4', 'r8', 'decimal'],
            self::PROPERTY_TYPE_STRING => ['empty', 'null', 'lpstr', 'lpwstr', 'bstr'],
            self::PROPERTY_TYPE_DATE => ['date', 'filetime'],
            self::PROPERTY_TYPE_BOOLEAN => ['bool'],
        ];
        foreach ($typeGroups as $groupId => $groupMembers) {
            if (in_array($propertyType, $groupMembers)) {
                return $groupId;
            }
        }

        return self::PROPERTY_TYPE_UNKNOWN;
    }

    /**
     * Set default for null and empty value.
     *
     * @param mixed $value
     * @param mixed $default
     *
     * @return mixed
     */
    private function setValue($value, $default)
    {
        if ($value === null || $value == '') {
            $value = $default;
        }

        return $value;
    }

    /**
     * Get conversion model depending on property type.
     *
     * @param string $propertyType
     *
     * @return string
     */
    private static function getConversion($propertyType)
    {
        $conversions = [
            'empty' => ['empty'],
            'null' => ['null'],
            'int' => ['i1', 'i2', 'i4', 'i8', 'int'],
            'uint' => ['ui1', 'ui2', 'ui4', 'ui8', 'uint'],
            'float' => ['r4', 'r8', 'decimal'],
            'bool' => ['bool'],
            'date' => ['date', 'filetime'],
        ];
        foreach ($conversions as $conversion => $types) {
            if (in_array($propertyType, $types)) {
                return $conversion;
            }
        }

        return 'string';
    }
}
