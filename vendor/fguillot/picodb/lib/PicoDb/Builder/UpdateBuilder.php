<?php

namespace PicoDb\Builder;

/**
 * Class UpdateBuilder
 *
 * @package PicoDb\Builder
 * @author  Frederic Guillot
 */
class UpdateBuilder extends BaseBuilder
{
    /**
     * @var string[]
     */
    protected $sumColumns = array();

    /**
     * Set columns name
     *
     * @access public
     * @param  string[] $columns
     * @return $this
     */
    public function withSumColumns(array $columns)
    {
        $this->sumColumns = $columns;
        return $this;
    }

    /**
     * Build SQL
     *
     * @access public
     * @return string
     */
    public function build()
    {
        $columns = array();

        foreach ($this->columns as $column) {
            $columns[] = $this->db->escapeIdentifier($column).'=?';
        }

        foreach ($this->sumColumns as $column) {
            $columns[] = $this->db->escapeIdentifier($column).'='.$this->db->escapeIdentifier($column).' + ?';
        }

        return sprintf(
            'UPDATE %s SET %s %s',
            $this->db->escapeIdentifier($this->table),
            implode(', ', $columns),
            $this->conditionBuilder->build()
        );
    }
}
