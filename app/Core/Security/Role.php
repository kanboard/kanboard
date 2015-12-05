<?php

namespace Kanboard\Core\Security;

/**
 * Role Definitions
 *
 * @package  security
 * @author   Frederic Guillot
 */
class Role
{
    const APP_ADMIN       = 'app-admin';
    const APP_MANAGER     = 'app-manager';
    const APP_USER        = 'app-user';
    const APP_PUBLIC      = 'app-public';

    const PROJECT_MANAGER = 'project-manager';
    const PROJECT_MEMBER  = 'project-member';
    const PROJECT_VIEWER  = 'project-viewer';
}
