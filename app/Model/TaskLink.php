<?php
namespace Model;

use Core\Helper;
use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PDO;

/**
 * TaskLink model
 *
 * @package model
 * @author Olivier Maridat
 */
class TaskLink extends Base
{

    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_links';

    /**
     * Get a link by the task link id
     *
     * @access public
     * @param integer $task_link_id
     *            Task link id
     * @return array
     */
    public function getById($task_link_id)
    {
        $sql = 'SELECT
            tl1.id AS id,
            tl1.link_label_id AS link_label_id,
            tl1.task_id AS task_id,
            tl1.task_inverse_id AS task_inverse_id,
            tl2.id AS task_link_inverse_id
            FROM ' . self::TABLE . ' tl1
            LEFT JOIN ' . Link::TABLE_LABEL . ' l1 ON l1.id = tl1.link_label_id
            LEFT JOIN ' . Link::TABLE_LABEL . ' l2 ON l2.link_id = l1.link_id
            LEFT JOIN ' . self::TABLE . ' tl2 ON tl2.task_id = tl1.task_inverse_id
            		AND ( (l1.behaviour = 2 AND tl2.link_label_id = l1.id) OR (tl2.link_label_id = l2.id) )
            WHERE tl1.id = ?
        ';
        $rq = $this->db->execute($sql, array(
            $task_link_id
        ));
        return $rq->fetch();
    }

    /**
     * Get the id of the inverse task link by a task link id
     *
     * @access public
     * @param integer $link_id
     *            Task link id
     * @return integer
     */
    public function getInverseTaskLinkId($task_link_id)
    {
        $sql = 'SELECT
            tl2.id
            FROM ' . self::TABLE . ' tl1
            LEFT JOIN ' . Link::TABLE_LABEL . ' l1 ON l1.id = tl1.link_label_id
            LEFT JOIN ' . Link::TABLE_LABEL . ' l2 ON l2.link_id = l1.link_id
            LEFT JOIN ' . self::TABLE . ' tl2 ON tl2.task_id = tl1.task_inverse_id
            		AND ( (l1.behaviour = 2 AND tl2.link_label_id = l1.id) OR (tl2.link_label_id = l2.id) )
            WHERE tl1.id = ?
        ';
        $rq = $this->db->execute($sql, array(
            $task_link_id
        ));
        return $rq->fetchColumn(0);
    }

    /**
     * Return all links for a given task
     *
     * @access public
     * @param integer $task_id
     *            Task id
     * @return array
     */
    public function getAll($task_id)
    {
        $sql = 'SELECT
            tl1.id,
            l.label AS label,
            t2.id AS task_inverse_id,
            t2.project_id AS task_inverse_project_id,
            t2.title AS task_inverse_title,
            t2.is_active AS task_inverse_is_active,
            t2cat.name AS task_inverse_category
            FROM ' . self::TABLE . ' tl1
            LEFT JOIN ' . Link::TABLE_LABEL . ' l ON l.id = tl1.link_label_id
            LEFT JOIN ' . Task::TABLE . ' t2 ON t2.id = tl1.task_inverse_id
            LEFT JOIN ' . Category::TABLE . ' t2cat ON t2cat.id = t2.category_id
            WHERE tl1.task_id = ?
            ORDER BY l.label, t2cat.name, t2.id
        ';
        $rq = $this->db->execute($sql, array(
            $task_id
        ));
        $res = $rq->fetchAll(PDO::FETCH_ASSOC);
        return $res;
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
        $this->removeFields($values, array(
            'another_link'
        ));
        $taskLink1 = array(
            'link_label_id' => $values['link_label_id'],
            'task_id' => $values['task_id'],
            'task_inverse_id' => $values['task_inverse_id']
        );
        $taskLink2 = array(
            'link_label_id' => $this->link->getInverseLinkLabelId($taskLink1['link_label_id']),
            'task_id' => $values['task_inverse_id'],
            'task_inverse_id' => $values['task_id']
        );
        if (isset($values['id']) && isset($values['task_link_inverse_id'])) {
            $taskLink1['id'] = $values['id'];
            $taskLink2['id'] = $values['task_link_inverse_id'];
        }
        return array(
            $taskLink1,
            $taskLink2
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
        list($taskLink1, $taskLink2) = $this->prepare($values);
        $this->db->startTransaction();
        if (! $this->db->table(self::TABLE)->save($taskLink1)) {
            $this->db->cancelTransaction();
            return false;
        }
        if (! $this->db->table(self::TABLE)->save($taskLink2)) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
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
        list($taskLink1, $taskLink2) = $this->prepare($values);
        $this->db->startTransaction();
        if (! $this->db->table(self::TABLE)
            ->eq('id', $taskLink1['id'])
            ->save($taskLink1)) {
            $this->db->cancelTransaction();
            return false;
        }
        if (! $this->db->table(self::TABLE)
            ->eq('id', $taskLink2['id'])
            ->save($taskLink2)) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Remove a link
     *
     * @access public
     * @param integer $task_link_id
     *            Task Link id
     * @return bool
     */
    public function remove($task_link_id)
    {
        $task_link_inverse_id = $this->getInverseTaskLinkId($task_link_id);
        $this->db->startTransaction();
        if (! $this->db->table(self::TABLE)
            ->eq('id', $task_link_id)
            ->remove()) {
            $this->db->cancelTransaction();
            return false;
        }
        if (! $this->db->table(self::TABLE)
            ->eq('id', $task_link_inverse_id)
            ->remove()) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return true;
    }

    /**
     * Duplicate all links to another task
     *
     * @access public
     * @param integer $src_task_id
     *            Source task id
     * @param integer $dst_task_id
     *            Destination task id
     * @return bool
     */
    public function duplicate($src_task_id, $dst_task_id)
    {
        return $this->db->transaction(function ($db) use($src_task_id, $dst_task_id)
        {
            $links = $db->table(TaskLink::TABLE)
                ->columns('link_label_id', 'task_id', 'task_inverse_id')
                ->eq('task_id', $src_task_id)
                ->asc('id')
                ->findAll();
            foreach ($links as &$link) {
                $link['task_id'] = $dst_task_id;
                if (! $db->table(TaskLink::TABLE)
                    ->save($link)) {
                    return false;
                }
            }
            
            $links = $db->table(TaskLink::TABLE)
                ->columns('link_label_id', 'task_id', 'task_inverse_id')
                ->eq('task_inverse_id', $src_task_id)
                ->asc('id')
                ->findAll();
            foreach ($links as &$link) {
                $link['task_inverse_id'] = $dst_task_id;
                if (! $db->table(TaskLink::TABLE)
                    ->save($link)) {
                    return false;
                }
            }
        });
    }

    /**
     * Move a task link from a link label to an other
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @param integer $dst_link_label_id
     *            Destination link label id
     * @return bool
     */
    public function changeLinkLabel($link_id, $dst_link_label_id, $alternate=false)
    {
        $taskLinks = $this->db->table(Link::TABLE_LABEL)
            ->eq('link_id', $link_id)
            ->neq(Link::TABLE_LABEL.'.id', $dst_link_label_id)
            ->join(self::TABLE, 'link_label_id', 'id')
            ->asc(self::TABLE.'.id')
            ->findAllByColumn(self::TABLE.'.id');
        foreach ($taskLinks as $i => $taskLinkId) {
            if (null == $taskLinkId || ($alternate && 0 != $i % 2)) {
                continue;
            }
            if (! $this->db->table(self::TABLE)
                ->eq('id', $taskLinkId)
                ->save(array('link_label_id' => $dst_link_label_id))) {
                return false;
            }
        }
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
        $res = array(
            $v->execute(),
            $v->getErrors()
        );
        return $res;
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
            new Validators\Required('id', t('The id is required'))
        );
        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        $res = array(
            $v->execute(),
            $v->getErrors()
        );
        return $res;
    }

    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Required('link_label_id', t('The link type is required')),
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('task_inverse_id', t('The linked task id is required')),
            new Validators\Integer('id', t('The id must be an integer')),
            new Validators\Integer('link_label_id', t('The link id must be an integer')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\Integer('task_inverse_id', t('The related task id must be an integer')),
            new Validators\Integer('task_link_inverse_id', t('The related task link id must be an integer')),
            new Validators\NotEquals('task_inverse_id', 'task_id', t('A task can not be linked to itself')),
            new Validators\Exists('task_inverse_id', t('This linked task id doesn\'t exist'), $this->db->getConnection(), Task::TABLE, 'id'),
            new Validators\Unique(array(
                'task_inverse_id',
                'link_label_id',
                'task_id'
            ), t('The exact same link already exists'), $this->db->getConnection(), self::TABLE)
        );
    }
}
