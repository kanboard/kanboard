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
        $dates = explode('..', $this->value);

        if(count($dates)=== 2){
            $timestampFrom = $this->dateParser->getTimestamp($dates[0]." 00:00");
            $timestampTo = $this->dateParser->getTimestamp($dates[1]." 00:00");

            $this->query->gte($field, $timestampFrom);
            $this->query->lte($field, $timestampTo + 86399);
        }
    }
}
