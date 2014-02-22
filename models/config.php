<?php

namespace Model;

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class Config extends Base
{
    const TABLE = 'config';

    public function getLanguages()
    {
        $languages = array(
            'en_US' => t('English'),
            'fr_FR' => t('French'),
            'pl_PL' => t('Polish'),
        );

        asort($languages);

        return $languages;
    }

    public function get($name, $default_value = '')
    {
        if (! isset($_SESSION['config'][$name])) {
            $_SESSION['config'] = $this->getAll();
        }

        if (isset($_SESSION['config'][$name])) {
            return $_SESSION['config'][$name];
        }

        return $default_value;
    }

    public function getAll()
    {
        return $this->db->table(self::TABLE)->findOne();
    }

    public function save(array $values)
    {
        $_SESSION['config'] = $values;
        return $this->db->table(self::TABLE)->update($values);
    }

    public function reload()
    {
        $_SESSION['config'] = $this->getAll();

        $language = $this->get('language', 'en_US');
        if ($language !== 'en_US') \Translator\load($language);
    }

    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('language', t('The language is required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function optimizeDatabase()
    {
        $this->db->getconnection()->exec("VACUUM");
    }

    public function downloadDatabase()
    {
        return gzencode(file_get_contents(self::DB_FILENAME));
    }

    public function getDatabaseSize()
    {
        return filesize(self::DB_FILENAME);
    }
}
