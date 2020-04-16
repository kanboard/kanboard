<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Layout Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class LayoutHelper extends Base
{
    /**
     * Render a template without the layout if Ajax request
     *
     * @access public
     * @param  string $template Template name
     * @param  array  $params   Template parameters
     * @return string
     */
    public function app($template, array $params = array())
    {
        $isAjax = $this->request->isAjax();
        $params['is_ajax'] = $isAjax;

        if ($isAjax) {
            return $this->template->render($template, $params);
        }

        if (! isset($params['no_layout']) && ! isset($params['board_selector'])) {
            $params['board_selector'] = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());

            if (isset($params['project']['id'])) {
                unset($params['board_selector'][$params['project']['id']]);
            }

            $this->hook->reference('helper:layout:board-selector:list', $params['board_selector']);
        }

        return $this->pageLayout($template, $params);
    }

    /**
     * Common layout for user views
     *
     * @access public
     * @param  string $template Template name
     * @param  array  $params   Template parameters
     * @return string
     */
    public function user($template, array $params)
    {
        if (isset($params['user'])) {
            $params['title'] = '#'.$params['user']['id'].' '.($params['user']['name'] ?: $params['user']['username']);
        }

        return $this->subLayout('user_view/layout', 'user_view/sidebar', $template, $params);
    }

    /**
     * Common layout for task views
     *
     * @access public
     * @param  string $template Template name
     * @param  array  $params   Template parameters
     * @return string
     */
    public function task($template, array $params)
    {
        $params['page_title'] = $params['task']['project_name'].', #'.$params['task']['id'].' - '.$params['task']['title'];
        $params['title'] = $params['task']['project_name'];
        return $this->subLayout('task/layout', 'task/sidebar', $template, $params);
    }

    /**
     * Common layout for project views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @param  string $sidebar
     * @return string
     */
    public function project($template, array $params, $sidebar = 'project/sidebar')
    {
        if (empty($params['title'])) {
            $params['title'] = $params['project']['name'];
        } elseif ($params['project']['name'] !== $params['title']) {
            $params['title'] = $params['project']['name'].' &gt; '.$params['title'];
        }

        return $this->subLayout('project/layout', $sidebar, $template, $params);
    }

    /**
     * Common layout for project user views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function projectUser($template, array $params)
    {
        $params['filter'] = array('user_id' => $params['user_id']);
        return $this->subLayout('project_user_overview/layout', 'project_user_overview/sidebar', $template, $params);
    }

    /**
     * Common layout for config views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function config($template, array $params)
    {
        if (! isset($params['values'])) {
            $params['values'] = $this->configModel->getAll();
        }

        if (! isset($params['errors'])) {
            $params['errors'] = array();
        }

        return $this->subLayout('config/layout', 'config/sidebar', $template, $params);
    }

    /**
     * Common layout for plugin views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function plugin($template, array $params)
    {
        return $this->subLayout('plugin/layout', 'plugin/sidebar', $template, $params);
    }

    /**
     * Common layout for dashboard views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function dashboard($template, array $params)
    {
        return $this->subLayout('dashboard/layout', 'dashboard/sidebar', $template, $params);
    }

    /**
     * Common layout for analytic views
     *
     * @access public
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function analytic($template, array $params)
    {
        if (isset($params['project']['name'])) {
            $params['title'] = $params['project']['name'].' &gt; '.$params['title'];
        }

        return $this->subLayout('analytic/layout', 'analytic/sidebar', $template, $params, true);
    }

    /**
     * Render page layout
     *
     * @access public
     * @param  string   $template   Template name
     * @param  array    $params     Key/value dictionary
     * @param  string   $layout     Layout name
     * @return string
     */
    public function pageLayout($template, array $params = array(), $layout = 'layout')
    {
        return $this->template->render(
            $layout,
            $params + array('content_for_layout' => $this->template->render($template, $params))
        );
    }

    /**
     * Common method to generate a sub-layout
     *
     * @access public
     * @param  string $sublayout
     * @param  string $sidebar
     * @param  string $template
     * @param  array  $params
     * @param  bool   $ignoreAjax
     * @return string
     */
    public function subLayout($sublayout, $sidebar, $template, array $params = array(), $ignoreAjax = false)
    {
        $isAjax = $this->request->isAjax();
        $params['is_ajax'] = $isAjax;
        $content = $this->template->render($template, $params);

        if (!$ignoreAjax && $isAjax) {
            return $content;
        }

        $params['content_for_sublayout'] = $content;
        $params['sidebar_template'] = $sidebar;

        return $this->app($sublayout, $params);
    }
}
