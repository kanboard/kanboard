<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\UserMetadataModel;

/**
 * Class CommentHelper
 *
 * @package Kanboard\Helper
 * @author  Frederic Guillot
 */
class CommentHelper extends Base
{
    public function toggleSorting()
    {
        $oldDirection = $this->userMetadataCacheDecorator->get(UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION, 'ASC');
        $newDirection = $oldDirection === 'ASC' ? 'DESC' : 'ASC';

        $this->userMetadataCacheDecorator->set(UserMetadataModel::KEY_COMMENT_SORTING_DIRECTION, $newDirection);
    }
}
