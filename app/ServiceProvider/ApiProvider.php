<?php

namespace Kanboard\ServiceProvider;

use JsonRPC\Server;
use Kanboard\Api\Procedure\ActionProcedure;
use Kanboard\Api\Procedure\AppProcedure;
use Kanboard\Api\Procedure\BoardProcedure;
use Kanboard\Api\Procedure\CategoryProcedure;
use Kanboard\Api\Procedure\ColumnProcedure;
use Kanboard\Api\Procedure\CommentProcedure;
use Kanboard\Api\Procedure\ProjectFileProcedure;
use Kanboard\Api\Procedure\ProjectMetadataProcedure;
use Kanboard\Api\Procedure\TagProcedure;
use Kanboard\Api\Procedure\TaskExternalLinkProcedure;
use Kanboard\Api\Procedure\TaskFileProcedure;
use Kanboard\Api\Procedure\GroupProcedure;
use Kanboard\Api\Procedure\GroupMemberProcedure;
use Kanboard\Api\Procedure\LinkProcedure;
use Kanboard\Api\Procedure\MeProcedure;
use Kanboard\Api\Middleware\AuthenticationMiddleware;
use Kanboard\Api\Procedure\ProjectProcedure;
use Kanboard\Api\Procedure\ProjectPermissionProcedure;
use Kanboard\Api\Procedure\SubtaskProcedure;
use Kanboard\Api\Procedure\SubtaskTimeTrackingProcedure;
use Kanboard\Api\Procedure\SwimlaneProcedure;
use Kanboard\Api\Procedure\TaskMetadataProcedure;
use Kanboard\Api\Procedure\TaskProcedure;
use Kanboard\Api\Procedure\TaskLinkProcedure;
use Kanboard\Api\Procedure\TaskTagProcedure;
use Kanboard\Api\Procedure\UserProcedure;
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
            ->withMiddleware(new AuthenticationMiddleware($container))
        ;

        $server->getProcedureHandler()
            ->withObject(new MeProcedure($container))
            ->withObject(new ActionProcedure($container))
            ->withObject(new AppProcedure($container))
            ->withObject(new BoardProcedure($container))
            ->withObject(new ColumnProcedure($container))
            ->withObject(new CategoryProcedure($container))
            ->withObject(new CommentProcedure($container))
            ->withObject(new TaskFileProcedure($container))
            ->withObject(new ProjectFileProcedure($container))
            ->withObject(new LinkProcedure($container))
            ->withObject(new ProjectProcedure($container))
            ->withObject(new ProjectPermissionProcedure($container))
            ->withObject(new ProjectMetadataProcedure($container))
            ->withObject(new SubtaskProcedure($container))
            ->withObject(new SubtaskTimeTrackingProcedure($container))
            ->withObject(new SwimlaneProcedure($container))
            ->withObject(new TaskProcedure($container))
            ->withObject(new TaskLinkProcedure($container))
            ->withObject(new TaskExternalLinkProcedure($container))
            ->withObject(new TaskMetadataProcedure($container))
            ->withObject(new TaskTagProcedure($container))
            ->withObject(new UserProcedure($container))
            ->withObject(new GroupProcedure($container))
            ->withObject(new GroupMemberProcedure($container))
            ->withObject(new TagProcedure($container))
            ->withBeforeMethod('beforeProcedure')
        ;

        $container['api'] = $server;
        return $container;
    }
}
