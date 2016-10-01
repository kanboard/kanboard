<?php

namespace Kanboard\Core;

use SplFileObject;

/**
 * CSV Writer/Reader
 *
 * @package core
 * @author  Frederic Guillot
 */
class Csv
{
    /**
     * CSV delimiter
     *
     * @access private
     * @var string
     */
    private $delimiter = ',';

    /**
     * CSV enclosure
     *
     * @access private
     * @var string
     */
    private $enclosure = '"';

    /**
     * CSV/SQL columns
     *
     * @access private
     * @var array
     */
    private $columns = array();

    /**
     * Constructor
     *
     * @access public
     * @param  string  $delimiter
     * @param  string  $enclosure
     */
    public function __construct($delimiter = ',', $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    /**
     * Get list of delimiters
     *
     * @static
     * @access public
     * @return array
     */
    public static function getDelimiters()
    {
        return array(
            ',' => t('Comma'),
            ';' => t('Semi-colon'),
            '\t' => t('Tab'),
            '|' => t('Vertical bar'),
        );
    }

    /**
     * Get list of enclosures
     *
     * @static
     * @access public
     * @return array
     */
    public static function getEnclosures()
    {
        return array(
            '"' => t('Double Quote'),
            "'" => t('Single Quote'),
            '' => t('None'),
        );
    }

    /**
     * Check boolean field value
     *
     * @static
     * @access public
     * @param  mixed $value
     * @return int
     */
    public static function getBooleanValue($value)
    {
        if (! empty($value)) {
            $value = trim(strtolower($value));
            return $value === '1' || $value{0} === 't' || $value{0} === 'y' ? 1 : 0;
        }

        return 0;
    }

    /**
     * Output CSV file to standard output
     *
     * @static
     * @access public
     * @param  array  $rows
     */
    public static function output(array $rows)
    {
        $csv = new static;
        $csv->write('php://output', $rows);
    }

    /**
     * Define column mapping between CSV and SQL columns
     *
     * @access public
     * @param  array $columns
     * @return Csv
     */
    public function setColumnMapping(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Read CSV file
     *
     * @access public
     * @param  string    $filename
     * @param  callable  $callback   Example: function(array $row, $line_number)
     * @return Csv
     */
    public function read($filename, $callback)
    {
        $file = new SplFileObject($filename);
        $file->setFlags(SplFileObject::READ_CSV);
        $file->setCsvControl($this->delimiter, $this->enclosure);
        $line_number = 0;

        foreach ($file as $row) {
            $row = $this->filterRow($row);

            if (! empty($row) && $line_number > 0) {
                call_user_func_array($callback, array($this->associateColumns($row), $line_number));
            }

            $line_number++;
        }

        return $this;
    }

    /**
     * Write CSV file
     *
     * @access public
     * @param  string    $filename
     * @param  array     $rows
     * @return Csv
     */
    public function write($filename, array $rows)
    {
        $fp = fopen($filename, 'w');

        if (is_resource($fp)) {
            foreach ($rows as $row) {
                fputcsv($fp, $row, $this->delimiter, $this->enclosure);
            }

            fclose($fp);
        }

        return $this;
    }

    /**
     * Associate columns header with row values
     *
     * @access private
     * @param  array $row
     * @return array
     */
    private function associateColumns(array $row)
    {
        $line = array();
        $index = 0;

        foreach ($this->columns as $sql_name => $csv_name) {
            if (isset($row[$index])) {
                $line[$sql_name] = $row[$index];
            } else {
                $line[$sql_name] = '';
            }

            $index++;
        }

        return $line;
    }

    /**
     * Filter empty rows
     *
     * @access private
     * @param  array $row
     * @return array
     */
    private function filterRow(array $row)
    {
        return array_filter($row);
    }
}
