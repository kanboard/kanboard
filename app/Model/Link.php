<?php

namespace Kanboard\Model;

use PDO;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Link model
 *
 * @package model
 * @author  Olivier Maridat
 * @author  Frederic Guillot
 */
class Link extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'links';

    /**
     * Get a link by id
     *
     * @access public
     * @param  integer   $link_id   Link id
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOne();
    }

    /**
     * Get a link by name
     *
     * @access public
     * @param  string $label
     * @return array
     */
    public function getByLabel($label)
    {
        return $this->db->table(self::TABLE)->eq('label', $label)->findOne();
    }

    /**
     * Get the opposite link id
     *
     * @access public
     * @param  integer   $link_id   Link id
     * @return integer
     */
    public function getOppositeLinkId($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOneColumn('opposite_id') ?: $link_id;
    }

    /**
     * Get all links
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db->table(self::TABLE)->findAll();
    }

    /**
     * Get merged links
     *
     * @access public
     * @return array
     */
    public function getMergedList()
    {
        return $this->db
                    ->execute('
                        SELECT
                            links.id, links.label, opposite.label as opposite_label
                        FROM links
                        LEFT JOIN links AS opposite ON opposite.id=links.opposite_id
                    ')
                    ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get label list
     *
     * @access public
     * @param  integer   $exclude_id   Exclude this link
     * @param  booelan   $prepend      Prepend default value
     * @return array
     */
    public function getList($exclude_id = 0, $prepend = true)
    {
        $labels = $this->db->hashtable(self::TABLE)->neq('id', $exclude_id)->asc('id')->getAll('id', 'label');

        foreach ($labels as &$value) {
            $value = t($value);
        }

        return $prepend ? array('') + $labels : $labels;
    }

    /**
     * Create a new link label
     *
     * @access public
     * @param  string   $label
     * @param  string   $opposite_label
     * @return boolean|integer
     */
    public function create($label, $opposite_label = '')
    {
        $this->db->startTransaction();

        if (! $this->db->table(self::TABLE)->insert(array('label' => $label))) {
            $this->db->cancelTransaction();
            return false;
        }

        $label_id = $this->db->getLastId();

        if (! empty($opposite_label)) {
            $this->db
                ->table(self::TABLE)
                ->insert(array(
                    'label' => $opposite_label,
                    'opposite_id' => $label_id,
                ));

            $this->db
                ->table(self::TABLE)
                ->eq('id', $label_id)
                ->update(array(
                    'opposite_id' => $this->db->getLastId()
                ));
        }

        $this->db->closeTransaction();

        return (int) $label_id;
    }

    /**
     * Update a link
     *
     * @access public
     * @param  array   $values
     * @return boolean
     */
    public function update(array $values)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $values['id'])
                    ->update(array(
                        'label' => $values['label'],
                        'opposite_id' => $values['opposite_id'],
                    ));
    }

    /**
     * Remove a link a the relation to its opposite
     *
     * @access public
     * @param  integer  $link_id
     * @return boolean
     */
    public function remove($link_id)
    {
        $this->db->table(self::TABLE)->eq('opposite_id', $link_id)->update(array('opposite_id' => 0));
        return $this->db->table(self::TABLE)->eq('id', $link_id)->remove();
    }

    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('label', t('Field required')),
            new Validators\Unique('label', t('This label must be unique'), $this->db->getConnection(), self::TABLE),
            new Validators\NotEquals('label', 'opposite_label', t('The labels must be different')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('Field required')),
            new Validators\Required('opposite_id', t('Field required')),
            new Validators\Required('label', t('Field required')),
            new Validators\Unique('label', t('This label must be unique'), $this->db->getConnection(), self::TABLE),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
