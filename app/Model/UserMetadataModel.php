<?php

namespace Kanboard\Model;

/**
 * User Metadata
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class UserMetadataModel extends MetadataModel
{
    const KEY_COMMENT_SORTING_DIRECTION = 'comment.sorting.direction';
    const KEY_BOARD_COLLAPSED = 'board.collapsed.';
    const KEY_TASK_SEARCH_ALL_FIELDS = 'task.search.all.fields';

    /**
     * Get the table
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getTable()
    {
        return 'user_has_metadata';
    }

    /**
     * Define the entity key
     *
     * @access protected
     * @return string
     */
    protected function getEntityKey()
    {
        return 'user_id';
    }
}
