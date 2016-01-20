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
     * Get available application currencies
     *
     * @access public
     * @return array
     */
    public function getCurrencies()
    {
        return array(
            'USD' => t('USD - US Dollar'),
            'EUR' => t('EUR - Euro'),
            'GBP' => t('GBP - British Pound'),
            'CHF' => t('CHF - Swiss Francs'),
            'CAD' => t('CAD - Canadian Dollar'),
            'AUD' => t('AUD - Australian Dollar'),
            'NZD' => t('NZD - New Zealand Dollar'),
            'INR' => t('INR - Indian Rupee'),
            'JPY' => t('JPY - Japanese Yen'),
            'RSD' => t('RSD - Serbian dinar'),
            'SEK' => t('SEK - Swedish Krona'),
            'NOK' => t('NOK - Norwegian Krone'),
            'BAM' => t('BAM - Konvertible Mark'),
            'RUB' => t('RUB - Russian Ruble'),
        );
    }

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
            $rates = $rates === null ? $this->db->hashtable(self::TABLE)->getAll('currency', 'rate') : $rates;
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

        return $this->db->table(self::TABLE)->insert(array('currency' => $currency, 'rate' => $rate));
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
