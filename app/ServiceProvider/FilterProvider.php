<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Filter\LexerBuilder;
use Kanboard\Core\Filter\QueryBuilder;
use Kanboard\Filter\ProjectActivityCreationDateFilter;
use Kanboard\Filter\ProjectActivityCreatorFilter;
use Kanboard\Filter\ProjectActivityProjectNameFilter;
use Kanboard\Filter\ProjectActivityTaskStatusFilter;
use Kanboard\Filter\ProjectActivityTaskTitleFilter;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskCategoryFilter;
use Kanboard\Filter\TaskColorFilter;
use Kanboard\Filter\TaskColumnFilter;
use Kanboard\Filter\TaskCommentFilter;
use Kanboard\Filter\TaskCompletionDateFilter;
use Kanboard\Filter\TaskCreationDateFilter;
use Kanboard\Filter\TaskCreatorFilter;
use Kanboard\Filter\TaskDescriptionFilter;
use Kanboard\Filter\TaskDueDateFilter;
use Kanboard\Filter\TaskStartDateFilter;
use Kanboard\Filter\TaskIdFilter;
use Kanboard\Filter\TaskLinkFilter;
use Kanboard\Filter\TaskModificationDateFilter;
use Kanboard\Filter\TaskMovedDateFilter;
use Kanboard\Filter\TaskPriorityFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskReferenceFilter;
use Kanboard\Filter\TaskScoreFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Filter\TaskSubtaskAssigneeFilter;
use Kanboard\Filter\TaskSwimlaneFilter;
use Kanboard\Filter\TaskTagFilter;
use Kanboard\Filter\TaskTitleFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectGroupRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Filter Provider
 *
 * @package Kanboard\ServiceProvider
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
        $this->createUserFilter($container);
        $this->createProjectFilter($container);
        $this->createTaskFilter($container);
        return $container;
    }

    public function createUserFilter(Container $container)
    {
        $container['userQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(UserModel::TABLE));
            return $builder;
        });

        return $container;
    }

    public function createProjectFilter(Container $container)
    {
        $container['projectGroupRoleQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(ProjectGroupRoleModel::TABLE));
            return $builder;
        });

        $container['projectUserRoleQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(ProjectUserRoleModel::TABLE));
            return $builder;
        });

        $container['projectQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['db']->table(ProjectModel::TABLE));
            return $builder;
        });

        $container['projectActivityLexer'] = $container->factory(function ($c) {
            $builder = new LexerBuilder();
            $builder
                ->withQuery($c['projectActivityModel']->getQuery())
                ->withFilter(new ProjectActivityTaskTitleFilter(), true)
                ->withFilter(new ProjectActivityTaskStatusFilter())
                ->withFilter(new ProjectActivityProjectNameFilter())
                ->withFilter(ProjectActivityCreationDateFilter::getInstance()
                    ->setDateParser($c['dateParser'])
                )
                ->withFilter(ProjectActivityCreatorFilter::getInstance()
                    ->setCurrentUserId($c['userSession']->getId())
                )
            ;

            return $builder;
        });

        $container['projectActivityQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['projectActivityModel']->getQuery());

            return $builder;
        });

        return $container;
    }

    public function createTaskFilter(Container $container)
    {
        $container['taskQuery'] = $container->factory(function ($c) {
            $builder = new QueryBuilder();
            $builder->withQuery($c['taskFinderModel']->getExtendedQuery());
            return $builder;
        });

        $container['taskLexer'] = $container->factory(function ($c) {
            $builder = new LexerBuilder();

            $builder
                ->withQuery($c['taskFinderModel']->getExtendedQuery())
                ->withFilter(TaskAssigneeFilter::getInstance()
                    ->setCurrentUserId($c['userSession']->getId())
                )
                ->withFilter(new TaskCategoryFilter())
                ->withFilter(TaskColorFilter::getInstance()
                    ->setColorModel($c['colorModel'])
                )
                ->withFilter(new TaskPriorityFilter())
                ->withFilter(new TaskColumnFilter())
                ->withFilter(new TaskCommentFilter())
                ->withFilter(TaskCreationDateFilter::getInstance()
                    ->setDateParser($c['dateParser'])
                )
                ->withFilter(TaskCreatorFilter::getInstance()
                    ->setCurrentUserId($c['userSession']->getId())
                )
                ->withFilter(new TaskDescriptionFilter())
                ->withFilter(TaskDueDateFilter::getInstance()
                    ->setDateParser($c['dateParser'])
                )
                ->withFilter(TaskStartDateFilter::getInstance()
                    ->setDateParser($c['dateParser'])
                )
                ->withFilter(TaskCompletionDateFilter::getInstance()
                    ->setDateparser($c['dateParser'])
                )
                ->withFilter(new TaskIdFilter())
                ->withFilter(TaskLinkFilter::getInstance()
                    ->setDatabase($c['db'])
                )
                ->withFilter(TaskModificationDateFilter::getInstance()
                    ->setDateParser($c['dateParser'])
                )
                ->withFilter(TaskMovedDateFilter::getInstance()
                    ->setDateParser($c['dateParser'])
                )
                ->withFilter(new TaskProjectFilter())
                ->withFilter(new TaskReferenceFilter())
                ->withFilter(new TaskScoreFilter())
                ->withFilter(new TaskStatusFilter())
                ->withFilter(TaskSubtaskAssigneeFilter::getInstance()
                    ->setCurrentUserId($c['userSession']->getId())
                    ->setDatabase($c['db'])
                )
                ->withFilter(new TaskSwimlaneFilter())
                ->withFilter(TaskTagFilter::getInstance()
                    ->setDatabase($c['db'])
                )
                ->withFilter(new TaskTitleFilter(), true)
            ;

            return $builder;
        });

        return $container;
    }
}
