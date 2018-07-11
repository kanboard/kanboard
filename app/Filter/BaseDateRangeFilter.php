<?php

namespace Kanboard\Filter;

use Kanboard\Core\DateParser;

/**
 * Base date filter class
 *
 * @package filter
 * @author  Kamil Ściana
 */
abstract class BaseDateRangeFilter extends BaseFilter
{
    /**
     * DateParser object
     *
     * @access protected
     * @var DateParser
     */
    protected $dateParser;

    /**
     * Set DateParser object
     *
     * @access public
     * @param  DateParser $dateParser
     * @return $this
     */
    public function setDateParser(DateParser $dateParser)
    {
        $this->dateParser = $dateParser;
        return $this;
    }

    /**
     * Apply a date filter
     *
     * @access protected
     * @param  string $field
     */
    protected function applyDateFilter($field)
    {
        $dates = explode(':', $this->value);
        
        
        $method = $this->parseOperator();
        $timestampFrom = $this->dateParser->getTimestampFromIsoFormat($dates[0]);
        $timestampTo = $this->dateParser->getTimestampFromIsoFormat($dates[1]);

        $this->query->gte($field, $timestampFrom);
        $this->query->lte($field, $timestampTo + 86399);        
    }
}
