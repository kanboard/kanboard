<?php

namespace Kanboard\ServiceProvider;

use JsonRPC\Server;
use Kanboard\Api\ActionApi;
use Kanboard\Api\AppApi;
use Kanboard\Api\BoardApi;
use Kanboard\Api\CategoryApi;
use Kanboard\Api\ColumnApi;
use Kanboard\Api\CommentApi;
use Kanboard\Api\FileApi;
use Kanboard\Api\GroupApi;
use Kanboard\Api\GroupMemberApi;
use Kanboard\Api\LinkApi;
use Kanboard\Api\MeApi;
use Kanboard\Api\Middleware\AuthenticationApiMiddleware;
use Kanboard\Api\ProjectApi;
use Kanboard\Api\ProjectPermissionApi;
use Kanboard\Api\SubtaskApi;
use Kanboard\Api\SubtaskTimeTrackingApi;
use Kanboard\Api\SwimlaneApi;
use Kanboard\Api\TaskApi;
use Kanboard\Api\TaskLinkApi;
use Kanboard\Api\UserApi;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ApiProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class ApiProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * @param Container $container
     * @return Container
     */
    public function register(Container $container)
    {
        $server = new Server();
        $server->setAuthenticationHeader(API_AUTHENTICATION_HEADER);
        $server->getMiddlewareHandler()
            ->withMiddleware(new AuthenticationApiMiddleware($container))
        ;

        $server->getProcedureHandler()
            ->withObject(new MeApi($container))
            ->withObject(new ActionApi($container))
            ->withObject(new AppApi($container))
            ->withObject(new BoardApi($container))
            ->withObject(new ColumnApi($container))
            ->withObject(new CategoryApi($container))
            ->withObject(new CommentApi($container))
            ->withObject(new FileApi($container))
            ->withObject(new LinkApi($container))
            ->withObject(new ProjectApi($container))
            ->withObject(new ProjectPermissionApi($container))
            ->withObject(new SubtaskApi($container))
            ->withObject(new SubtaskTimeTrackingApi($container))
            ->withObject(new SwimlaneApi($container))
            ->withObject(new TaskApi($container))
            ->withObject(new TaskLinkApi($container))
            ->withObject(new UserApi($container))
            ->withObject(new GroupApi($container))
            ->withObject(new GroupMemberApi($container))
        ;
        
        $container['api'] = $server;
        return $container;
    }
}
