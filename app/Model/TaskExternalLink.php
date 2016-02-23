<?php

namespace Kanboard\Model;

/**
 * Task External Link Model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskExternalLink extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_external_links';

    /**
     * Get all links
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getAll($task_id)
    {
        $types = $this->externalLinkManager->getTypes();

        $links = $this->db->table(self::TABLE)
            ->columns(self::TABLE.'.*', User::TABLE.'.name AS creator_name', User::TABLE.'.username AS creator_username')
            ->eq('task_id', $task_id)
            ->asc('title')
            ->join(User::TABLE, 'id', 'creator_id')
            ->findAll();

        foreach ($links as &$link) {
            $link['dependency_label'] = $this->externalLinkManager->getDependencyLabel($link['link_type'], $link['dependency']);
            $link['type'] = isset($types[$link['link_type']]) ? $types[$link['link_type']] : t('Unknown');
        }

        return $links;
    }

    /**
     * Get link
     *
     * @access public
     * @param  integer $link_id
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOne();
    }

    /**
     * Add a new link in the database
     *
     * @access public
     * @param  array  $values  Form values
     * @return boolean|integer
     */
    public function create(array $values)
    {
        unset($values['id']);
        $values['creator_id'] = $this->userSession->getId();
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];

        return $this->persist(self::TABLE, $values);
    }

    /**
     * Modify external link
     *
     * @access public
     * @param  array  $values  Form values
     * @return boolean
     */
    public function update(array $values)
    {
        $values['date_modification'] = time();
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);
    }

    /**
     * Remove a link
     *
     * @access public
     * @param  integer $link_id
     * @return boolean
     */
    public function remove($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->remove();
    }
}
