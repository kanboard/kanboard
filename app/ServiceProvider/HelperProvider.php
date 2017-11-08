<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Helper;
use Kanboard\Core\Template;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class HelperProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class HelperProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['helper'] = new Helper($container);
        $container['helper']->register('app', '\Kanboard\Helper\AppHelper');
        $container['helper']->register('asset', '\Kanboard\Helper\AssetHelper');
        $container['helper']->register('board', '\Kanboard\Helper\BoardHelper');
        $container['helper']->register('comment', '\Kanboard\Helper\CommentHelper');
        $container['helper']->register('dt', '\Kanboard\Helper\DateHelper');
        $container['helper']->register('file', '\Kanboard\Helper\FileHelper');
        $container['helper']->register('form', '\Kanboard\Helper\FormHelper');
        $container['helper']->register('hook', '\Kanboard\Helper\HookHelper');
        $container['helper']->register('layout', '\Kanboard\Helper\LayoutHelper');
        $container['helper']->register('model', '\Kanboard\Helper\ModelHelper');
        $container['helper']->register('subtask', '\Kanboard\Helper\SubtaskHelper');
        $container['helper']->register('task', '\Kanboard\Helper\TaskHelper');
        $container['helper']->register('text', '\Kanboard\Helper\TextHelper');
        $container['helper']->register('url', '\Kanboard\Helper\UrlHelper');
        $container['helper']->register('user', '\Kanboard\Helper\UserHelper');
        $container['helper']->register('avatar', '\Kanboard\Helper\AvatarHelper');
        $container['helper']->register('projectRole', '\Kanboard\Helper\ProjectRoleHelper');
        $container['helper']->register('projectHeader', '\Kanboard\Helper\ProjectHeaderHelper');
        $container['helper']->register('projectActivity', '\Kanboard\Helper\ProjectActivityHelper');
        $container['helper']->register('mail', '\Kanboard\Helper\MailHelper');
        $container['helper']->register('modal', '\Kanboard\Helper\ModalHelper');

        $container['template'] = new Template($container['helper']);

        return $container;
    }
}
