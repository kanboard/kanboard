<?php
namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PDO;

/**
 * Link model
 * A link is made of one bidirectional (<->), or two sided (<- and ->) link labels.
 *
 * @package model
 * @author Olivier Maridat
 */
class Link extends Base
{

    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'link';

    const TABLE_LABEL = 'link_label';

    /**
     * Direction: left to right ->
     *
     * @var integer
     */
    const BEHAVIOUR_LEFT2RIGTH = 0;

    /**
     * Direction: right to left <-
     *
     * @var integer
     */
    const BEHAVIOUR_RIGHT2LEFT = 1;

    /**
     * Bidirectional <->
     *
     * @var integer
     */
    const BEHAVIOUR_BOTH = 2;

    /**
     * Get a link by the id
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @param integer $project_id
     *            Specify a project id. Default: -1 to target all projects
     * @return array
     */
    public function getById($link_id, $project_id = -1)
    {
        return $this->db->table(self::TABLE)
            ->eq(self::TABLE . '.link_id', $link_id)
            ->in('project_id', array(
            - 1,
            $project_id
        ))
            ->join(self::TABLE_LABEL, 'link_id', 'link_id')
            ->findAll();
    }

    /**
     * Get the id of the inverse link label by a link label id
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @param integer $link_label_id
     *            Link label id
     * @return integer
     */
    public function getInverseLinkLabelId($link_label_id)
    {
        $sql = 'SELECT
            la2.id
            FROM ' . self::TABLE_LABEL . ' la1
            JOIN '.self::TABLE_LABEL.' la2 ON la2.link_id = la1.link_id AND (la2.behaviour=2 OR la2.id != la1.id)
            WHERE la1.id = ?
        ';
        $rq = $this->db->execute($sql, array(
            $link_label_id
        ));
        return $rq->fetchColumn(0);
    }

    /**
     * Return all link labels for a given project
     *
     * @access public
     * @param integer $project_id
     *            Specify a project id. Default: -1 to target all projects
     * @return array
     */
    public function getLinkLabels($project_id = -1)
    {
        return $this->db->table(self::TABLE_LABEL)
            ->in(self::TABLE . '.project_id', array(
            - 1,
            $project_id
        ))
            ->join(self::TABLE, 'link_id', 'link_id')
            ->asc(self::TABLE_LABEL.'.link_id', 'behaviour')
            ->columns('id', self::TABLE . '.project_id', self::TABLE_LABEL.'.link_id', 'label', 'behaviour')
            ->findAll();
    }

    /**
     * Return the list of all link labels
     * Used to select a link label
     *
     * @access public
     * @param integer $project_id
     *            Specify a project id. Default: -1 to target all projects
     * @return array
     */
    public function getLinkLabelList($project_id = -1)
    {
        $listing = $this->getLinkLabels($project_id);
        $mergedListing = array();
        foreach ($listing as $link) {
            $suffix = '';
            $prefix = '';
            if (self::BEHAVIOUR_BOTH == $link['behaviour'] || self::BEHAVIOUR_LEFT2RIGTH == $link['behaviour']) {
                $suffix = ' &raquo;';
            }
            if (self::BEHAVIOUR_BOTH == $link['behaviour'] || self::BEHAVIOUR_RIGHT2LEFT == $link['behaviour']) {
                $prefix = '&laquo; ';
            }
            $mergedListing[$link['id']] = $prefix . t($link['label']) . $suffix;
        }
        $listing = $mergedListing;
        return $listing;
    }

    /**
     * Return the list of all links (label + inverse label)
     *
     * @access public
     * @param integer $project_id
     *            Specify a project id. Default: -1 to target all projects
     * @return array
     */
    public function getMergedList($project_id = -1)
    {
        $listing = $this->getLinkLabels($project_id);
        $mergedListing = array();
        $current = null;
        foreach ($listing as $link) {
            if (self::BEHAVIOUR_BOTH == $link['behaviour'] || self::BEHAVIOUR_LEFT2RIGTH == $link['behaviour']) {
                $current = $link;
            }
            else {
                $current['label_inverse'] = $link['label'];
            }
            if (self::BEHAVIOUR_BOTH == $link['behaviour'] || self::BEHAVIOUR_RIGHT2LEFT == $link['behaviour']) {
                $mergedListing[] = $current;
                $current = null;
            }
        }
        $listing = $mergedListing;
        return $listing;
    }

    /**
     * Prepare data before insert/update
     *
     * @access public
     * @param array $values
     *            Form values
     */
    public function prepare(array &$values)
    {
        // Prepare label 1
        $link = array(
            'project_id' => $values['project_id']
        );
        $label1 = array(
            'label' => @$values['label'][0],
            'behaviour' => (isset($values['behaviour'][0]) || !isset($values['label'][1]) || null == $values['label'][1]) ? self::BEHAVIOUR_BOTH : self::BEHAVIOUR_LEFT2RIGTH
        );
        $label2 = array(
            'label' => @$values['label'][1],
            'behaviour' => self::BEHAVIOUR_RIGHT2LEFT
        );
        if (isset($values['link_id'])) {
            $link['link_id'] = $values['link_id'];
            $label1['id'] = $values['id'][0];
            $label2['id'] = @$values['id'][1];
            $label1['link_id'] = $values['link_id'];
            $label2['link_id'] = $values['link_id'];
        }
        
        $values = $link;
        $values[] = $label1;
        $values[] = $label2;
        return array(
            $link,
            $label1,
            $label2
        );
    }

    /**
     * Create a link
     *
     * @access public
     * @param array $values
     *            Form values
     * @return bool integer
     */
    public function create(array $values)
    {
        list ($link, $label1, $label2) = $this->prepare($values);
        // Create link
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)->save($link);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        
        // Create label 1
        $label1['link_id'] = $this->db->getConnection()->lastInsertId(self::TABLE);
        $res = $this->db->table(self::TABLE_LABEL)->save($label1);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        
        // Create label 2 if any
        if (null != $label2 && self::BEHAVIOUR_BOTH != $label1['behaviour']) {
            $label2['link_id'] = $label1['link_id'];
            $res = $this->db->table(self::TABLE_LABEL)->save($label2);
            if (! $res) {
                $this->db->cancelTransaction();
                return false;
            }
        }
        $this->db->closeTransaction();
        return $res;
    }

    /**
     * Update a link
     *
     * @access public
     * @param array $values
     *            Form values
     * @return bool
     */
    public function update(array $values)
    {
        list($link, $label1, $label2) = $this->prepare($values);
        // Update link
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)
            ->eq('link_id', $link['link_id'])
            ->save($link);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        
        // Update label 1
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE_LABEL)
            ->eq('id', $label1['id'])
            ->save($label1);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        
        // Update label 2 (if label 1 not bidirectional)
        if (null != $label2 && self::BEHAVIOUR_BOTH != $label1['behaviour']) {
            // Create
            if (! isset($label2['id']) || null == $label2['id']) {
                unset($label2['id']);
                $res = $this->db->table(self::TABLE_LABEL)->save($label2);
                if (! $res) {
                    $this->db->cancelTransaction();
                    return false;
                }
                $label2['id'] = $this->db->getConnection()->lastInsertId(self::TABLE_LABEL);
                $this->taskLink->changeLinkLabel($link['link_id'], $label2['id'], true);
            }
            // Update
            else {
                $res = $this->db->table(self::TABLE_LABEL)
                    ->eq('id', $label2['id'])
                    ->save($label2);
                if (! $res) {
                    $this->db->cancelTransaction();
                    return false;
                }
            }
        }
        // Remove label 2 (if label 1 bidirectional)
        else {
            $this->taskLink->changeLinkLabel($link['link_id'], $label1['id']);
            $this->db->table(self::TABLE_LABEL)
                ->eq('link_id', $link['link_id'])
                ->neq('id', $label1['id'])
                ->remove();
        }
        $this->db->closeTransaction();
        return $res;
    }

    /**
     * Remove a link
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @return bool
     */
    public function remove($link_id)
    {
        $this->db->startTransaction();
        if (! $this->db->table(self::TABLE)
            ->eq('link_id', $link_id)
            ->remove()) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Duplicate links from a project to another one, must be executed inside a transaction
     *
     * @param integer $src_project_id
     *            Source project id
     * @return integer $dst_project_id Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $labels = $this->db->table(self::TABLE_LABEL)
            ->columns(self::TABLE_LABEL.'.link_id', 'label', 'behaviour')
            ->eq('project_id', $src_project_id)
            ->join(self::TABLE, 'link_id', 'link_id')
            ->asc(self::TABLE_LABEL.'.link_id', 'behaviour')
            ->findAll();
        
        $this->db->startTransaction();
        $link = array('project_id' => $dst_project_id);
        if (! $this->db->table(self::TABLE)->save($link)) {
        	$this->db->cancelTransaction();
        	return false;
        }
        $link['id'] = $this->db->getConnection()->lastInsertId(self::TABLE);
        
        foreach ($labels as $label) {
            $label['link_id'] = $link['id'];
            if (! $this->db->table(self::TABLE_LABEL)->save($label)) {
            	$this->db->cancelTransaction();
                return false;
            }
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Validate link creation
     *
     * @access public
     * @param array $values
     *            Form values
     * @return array $valid, $errors [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, $this->commonValidationRules());
        
        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate link modification
     *
     * @access public
     * @param array $values
     *            Form values
     * @return array $valid, $errors [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('link_id', t('The id is required')),
//             new Validators\Required('id[0]', t('The label id is required'))
        );
        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        
        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        // TODO Update simple-validator to support array in forms
        return array(
            new Validators\Required('project_id', t('The project id required')),
            // new Validators\Required('label[0]', t('The link label is required')),
            new Validators\Integer('project_id', t('The project id must be an integer')),
            new Validators\Integer('link_id', t('The link id must be an integer')),
//             new Validators\Integer('id[0]', t('The link label id must be an integer')),
//             new Validators\Integer('id[1]', t('The link label id must be an integer')),
//             new Validators\Integer('behaviour[0]', t('The link label id must be an integer')),
//             new Validators\Integer('behaviour[1]', t('The link label id must be an integer')),
//             new Validators\MaxLength('label[0]', t('The maximum length is %d characters', 200), 200),
//             new Validators\MaxLength('label[1]', t('The maximum length is %d characters', 200), 200)
        );
    }
}
