<?php

namespace Kanboard\Model;

/**
 * Currency
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Currency extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'currencies';

    /**
     * Get all currency rates
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db->table(self::TABLE)->findAll();
    }

    /**
     * Calculate the price for the reference currency
     *
     * @access public
     * @param  string  $currency
     * @param  double  $price
     * @return double
     */
    public function getPrice($currency, $price)
    {
        static $rates = null;
        $reference = $this->config->get('application_currency', 'USD');

        if ($reference !== $currency) {
            $rates = $rates === null ? $this->db->hashtable(self::TABLE)->getAll('currency', 'rate') : array();
            $rate = isset($rates[$currency]) ? $rates[$currency] : 1;

            return $rate * $price;
        }

        return $price;
    }

    /**
     * Add a new currency rate
     *
     * @access public
     * @param  string    $currency
     * @param  float     $rate
     * @return boolean|integer
     */
    public function create($currency, $rate)
    {
        if ($this->db->table(self::TABLE)->eq('currency', $currency)->exists()) {
            return $this->update($currency, $rate);
        }

        return $this->persist(self::TABLE, compact('currency', 'rate'));
    }

    /**
     * Update a currency rate
     *
     * @access public
     * @param  string    $currency
     * @param  float     $rate
     * @return boolean
     */
    public function update($currency, $rate)
    {
        return $this->db->table(self::TABLE)->eq('currency', $currency)->update(array('rate' => $rate));
    }
}
