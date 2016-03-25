<div class="project-header">
    <?= $this->hook->render('template:project:header:before', array('project' => $project)) ?>

    <?= $this->render('project_header/dropdown', array('project' => $project, 'board_view' => $board_view)) ?>
    <?= $this->render('project_header/views', array('project' => $project, 'filters' => $filters)) ?>
    <?= $this->render('project_header/search', array(
        'project' => $project,
        'filters' => $filters,
        'custom_filters_list' => isset($custom_filters_list) ? $custom_filters_list : array(),
        'users_list' => isset($users_list) ? $users_list : array(),
        'categories_list' => isset($categories_list) ? $categories_list : array(),
    )) ?>

    <?= $this->hook->render('template:project:header:after', array('project' => $project)) ?>
</div>