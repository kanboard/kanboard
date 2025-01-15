<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Currency
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class CurrencyModel extends Base
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
            'ARS' => t('ARS - Argentine Peso'),
            'AUD' => t('AUD - Australian Dollar'),
            'BAM' => t('BAM - Konvertible Mark'),
            'BRL' => t('BRL - Brazilian Real'),
            'CAD' => t('CAD - Canadian Dollar'),
            'CHF' => t('CHF - Swiss Francs'),
            'CNY' => t('CNY - Chinese Yuan'),
            'COP' => t('COP - Colombian Peso'),
            'DKK' => t('DKK - Danish Krona'),
            'EUR' => t('EUR - Euro'),
            'GBP' => t('GBP - British Pound'),
            'HRK' => t('HRK - Kuna'),
            'HUF' => t('HUF - Hungarian Forint'),
            'INR' => t('INR - Indian Rupee'),
            'JPY' => t('JPY - Japanese Yen'),
            'MXN' => t('MXN - Mexican Peso'),
            'NOK' => t('NOK - Norwegian Krone'),
            'NZD' => t('NZD - New Zealand Dollar'),
            'PEN' => t('PEN - Peruvian Sol'),
            'RSD' => t('RSD - Serbian dinar'),
            'RUB' => t('RUB - Russian Ruble'),
            'SEK' => t('SEK - Swedish Krona'),
            'TRL' => t('TRL - Turkish Lira'),
            'USD' => t('USD - US Dollar'),
            'VBL' => t('VES - Venezuelan Bolívar'),
            'XBT' => t('XBT - Bitcoin'),
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
        $reference = $this->configModel->get('application_currency', 'USD');

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
