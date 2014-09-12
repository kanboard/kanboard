<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Core\Translator;
use Core\Security;

/**
 * Config model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Config extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'config';

    /**
     * Get available timezones
     *
     * @access public
     * @return array
     */
    public function getTimezones()
    {
        $timezones = timezone_identifiers_list();
        return array_combine(array_values($timezones), $timezones);
    }

    /**
     * Get available languages
     *
     * @access public
     * @return array
     */
    public function getLanguages()
    {
        // Sorted by value
        return array(
            'de_DE' => 'Deutsch',
            'en_US' => 'English',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'it_IT' => 'Italiano',
            'pl_PL' => 'Polski',
            'pt_BR' => 'Português (Brasil)',
            'ru_RU' => 'Русский',
            'fi_FI' => 'Suomi',
            'sv_SE' => 'Svenska',
            'zh_CN' => '中文(简体)',
        );
    }

    /**
     * Get a config variable from the session or the database
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  string   $default_value   Default value of the parameter
     * @return string
     */
    public function get($name, $default_value = '')
    {
        if (! isset($_SESSION['config'][$name])) {
            $_SESSION['config'] = $this->getAll();
        }

        if (! empty($_SESSION['config'][$name])) {
            return $_SESSION['config'][$name];
        }

        return $default_value;
    }

    /**
     * Get all settings
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db->table(self::TABLE)->findOne();
    }

    /**
     * Save settings in the database
     *
     * @access public
     * @param  $values  array   Settings values
     * @return boolean
     */
    public function save(array $values)
    {
        $_SESSION['config'] = $values;
        return $this->db->table(self::TABLE)->update($values);
    }

    /**
     * Reload settings in the session and the translations
     *
     * @access public
     */
    public function reload()
    {
        $_SESSION['config'] = $this->getAll();
        Translator::load($this->get('language', 'en_US'));
    }

    /**
     * Validate settings modification
     *
     * @access public
     * @param  array    $values           Form values
     * @return array    $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('language', t('The language is required')),
            new Validators\Required('timezone', t('The timezone is required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Optimize the Sqlite database
     *
     * @access public
     * @return boolean
     */
    public function optimizeDatabase()
    {
        return $this->db->getconnection()->exec("VACUUM");
    }

    /**
     * Compress the Sqlite database
     *
     * @access public
     * @return string
     */
    public function downloadDatabase()
    {
        return gzencode(file_get_contents(DB_FILENAME));
    }

    /**
     * Get the Sqlite database size in bytes
     *
     * @access public
     * @return integer
     */
    public function getDatabaseSize()
    {
        return DB_DRIVER === 'sqlite' ? filesize(DB_FILENAME) : 0;
    }

    /**
     * Regenerate all tokens (projects and webhooks)
     *
     * @access public
     */
    public function regenerateTokens()
    {
        $this->db->table(self::TABLE)->update(array(
            'webhooks_token' => Security::generateToken(),
            'api_token' => Security::generateToken(),
        ));

        $projects = $this->db->table(Project::TABLE)->findAllByColumn('id');

        foreach ($projects as $project_id) {
            $this->db->table(Project::TABLE)->eq('id', $project_id)->update(array('token' => Security::generateToken()));
        }
    }
}
