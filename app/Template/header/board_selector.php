<?= $this->app->component('select-dropdown-autocomplete', array(
    'name' => 'boardId',
    'placeholder' => t('Display another project'),
    'ariaLabel' => t('Display another project'),
    'items' => $board_selector,
    'redirect' => array(
        'regex' => 'PROJECT_ID',
        'url' => $this->url->to('BoardViewController', 'show', array('project_id' => 'PROJECT_ID')),
    ),
    'onFocus' => array(
        'board.selector.open',
    )
)) ?>

