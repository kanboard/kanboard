<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Filter\LexerBuilder;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskCategoryFilter;
use Kanboard\Filter\TaskColorFilter;
use Kanboard\Filter\TaskColumnFilter;
use Kanboard\Filter\TaskCommentFilter;
use Kanboard\Filter\TaskCreationDateFilter;
use Kanboard\Filter\TaskDescriptionFilter;
use Kanboard\Filter\TaskDueDateFilter;
use Kanboard\Filter\TaskIdFilter;
use Kanboard\Filter\TaskLinkFilter;
use Kanboard\Filter\TaskModificationDateFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskReferenceFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Filter\TaskSubtaskAssigneeFilter;
use Kanboard\Filter\TaskSwimlaneFilter;
use Kanboard\Filter\TaskTitleFilter;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectGroupRole;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Model\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Filter Provider
 *
 * @package serviceProvider
 * @author  Frederic Guillot
 */
class FilterProvider implements ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @access public
     * @param  \Pimple\Container $container
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['projectGroupRoleQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(ProjectGroupRole::TABLE));
            return $builder;
        });

        $container['projectUserRoleQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(ProjectUserRole::TABLE));
            return $builder;
        });

        $container['userQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(User::TABLE));
            return $builder;
        });

        $container['projectQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(Project::TABLE));
            return $builder;
        });

        $container['taskQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['taskFinder']->getExtendedQuery());
            return $builder;
        });

        $container['taskLexer'] = $container->factory(function ($c) {
            $builder = new LexerBuilder();

            $builder
                ->withQuery($c['taskFinder']->getExtendedQuery())
                ->withFilter(TaskAssigneeFilter::getInstance()
                    ->setCurrentUserId($c['userSession']->getId())
                )
                ->withFilter(new TaskCategoryFilter())
                ->withFilter(TaskColorFilter::getInstance()->setColorModel($c['color']))
                ->withFilter(new TaskColumnFilter())
                ->withFilter(new TaskCommentFilter())
                ->withFilter(new TaskCreationDateFilter())
                ->withFilter(new TaskDescriptionFilter())
                ->withFilter(new TaskDueDateFilter())
                ->withFilter(new TaskIdFilter())
                ->withFilter(TaskLinkFilter::getInstance()
                    ->setDatabase($c['db'])
                )
                ->withFilter(new TaskModificationDateFilter())
                ->withFilter(new TaskProjectFilter())
                ->withFilter(new TaskReferenceFilter())
                ->withFilter(new TaskStatusFilter())
                ->withFilter(TaskSubtaskAssigneeFilter::getInstance()
                    ->setCurrentUserId($c['userSession']->getId())
                    ->setDatabase($c['db'])
                )
                ->withFilter(new TaskSwimlaneFilter())
                ->withFilter(new TaskTitleFilter(), true)
            ;

            return $builder;
        });

        return $container;
    }
}
