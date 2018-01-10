Plugin Hooks
============

Application Hooks
-----------------

Hooks can extend, replace, filter data or change the default behavior. Each hook is identified with a unique name, example: `controller:calendar:user:events`

### Listen on hook events

In your `initialize()` method you need to call the method `on()` of the class `Kanboard\Core\Plugin\Hook`:

```php
$this->hook->on('hook_name', $callable);
```

The first argument is the name of the hook and the second is a PHP callable.

### Hooks executed only once

Some hooks can have only one listener:

#### model:subtask-time-tracking:calculate:time-spent

- Override time spent calculation when sub-task timer is stopped
- Arguments:
    - `$user_id` (integer)
    - `$start` (DateTime)
    - `$end` (DateTime)

### Merge hooks

"Merge hooks" act in the same way as the function `array_merge`. The hook callback must return an array. This array will be merged with the default one.

Example to add events in the user calendar:

```php
class Plugin extends Base
{
    public function initialize()
    {
        $container = $this->container;

        $this->hook->on('controller:calendar:user:events', function($user_id, $start, $end) use ($container) {
            $model = new SubtaskForecast($container);
            return $model->getCalendarEvents($user_id, $end); // Return new events
        });
    }
}
```

Example to override default values for task forms:

```php
class Plugin extends Base
{
    public function initialize()
    {
        $this->hook->on('controller:task:form:default', function (array $default_values) {
            return empty($default_values['score']) ? array('score' => 4) : array();
        });
    }
}
```

List of merging hooks:

`controller:task:form:default`

- Override default values for task forms
- Arguments:
    - `$default_values`: actual default values (array)

`controller:calendar:project:events`

- Add more events to the project calendar
- Arguments:
    - `$project_id` (integer)
    - `$start` Calendar start date (string, ISO-8601 format)
    - `$end` Calendar` end date (string, ISO-8601 format)

`controller:calendar:user:events`

- Add more events to the user calendar
- Arguments:
    - `$user_id` (integer)
    - `$start` Calendar start date (string, ISO-8601 format)
    - `$end` Calendar end date (string, ISO-8601 format)

Asset Hooks
-----------

Asset hooks can be used to add a new stylesheet easily or a new JavaScript file in the layout. You can use this feature to create a theme and override all Kanboard default styles.

Example to add a new stylesheet:

```php
<?php

namespace Kanboard\Plugin\Css;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->hook->on('template:layout:css', array('template' => 'plugins/Css/skin.css'));
    }
}
```

List of asset Hooks:

- `template:layout:css`
- `template:layout:js`


Reference hooks
---------------

Reference hooks are passing a variable by reference.

Example:

```php
$this->hook->on('formatter:board:query', function (\PicoDb\Table &query) {
    $query->eq('color_id', 'red');
});
```

The code above will show only tasks in red on the board.

List of reference hooks:

| Hook                                       | Description                                                   |
|--------------------------------------------|---------------------------------------------------------------|
| `formatter:board:query`                    | Alter database query before rendering board                   |
| `pagination:dashboard:project:query`       | Alter database query for projects pagination on the dashboard |
| `pagination:dashboard:task:query`          | Alter database query for tasks pagination on the dashboard    |
| `pagination:dashboard:subtask:query`       | Alter database query for subtasks pagination on the dashboard |
| `model:task:creation:prepare`              | Alter form values before to save a task                       |
| `model:task:creation:aftersave`            | Retrieve Task ID after creating a task                        |
| `model:task:modification:prepare`          | Alter form values before to edit a task                       |
| `model:color:get-list`                     | Alter default_colors values                                   |
| `model:subtask:modification:prepare`       | Alter form values before to save a subtask                    |
| `model:subtask:creation:prepare`           | Alter form values before to edit a subtask                    |
| `model:subtask:count:query`                | Alter database query for subtask count                        |


Template Hooks
--------------

Template hooks allow to add new content in existing templates.

Example to add new content in the dashboard sidebar:

```php
$this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar');
```

Example to attach a template with local variables:

```php
$this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar', array(
    'variable' => 'foobar',
));
```

Example to attach a template with a callable:

```php
$this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar', function($hook_param1, $hook_param2) {
    return array('new_template_variable' => 'foobar'); // Inject a new variable into the plugin template
});
```

This call is usually defined in the `initialize()` method.
The first argument is name of the hook and the second argument is the template name.

Template names prefixed with the plugin name and colon indicate the location of the template.

Example with `myplugin:dashboard/sidebar`:

- `myplugin` is the name of your plugin (lowercase)
- `dashboard/sidebar` is the template name
- On the filesystem, the plugin will be located here: `plugins\Myplugin\Template\dashboard\sidebar.php`
- Templates are written in pure PHP (don't forget to escape data)

Template names without prefix are core templates.

List of template hooks:

| Hook                                                      | Description                                        |
|-----------------------------------------------------------|----------------------------------------------------|
| `template:analytic:sidebar`                               | Sidebar on analytic pages                          |
| `template:app:filters-helper:before`                      | Filter helper dropdown (top)                       |
| `template:app:filters-helper:after`                       | Filter helper dropdown (bottom)                    |
| `template:auth:login-form:before`                         | Login page (top)                                   |
| `template:auth:login-form:after`                          | Login page (bottom)                                |
| `template:board:private:task:before-title`                | Task in private board: before title                |
| `template:board:private:task:after-title`                 | Task in private board: after title                 |
| `template:board:public:task:before-title`                 | Task in public board: before title                 |
| `template:board:public:task:after-title`                  | Task in public board: after title                  |
| `template:board:task:footer`                              | Task in board: footer                              |
| `template:board:task:icons`                               | Task in board: tooltip icon                        |
| `template:board:table:column:before-header-row`           | Row before board column header                     |
| `template:board:table:column:after-header-row`            | Row after board column header                      |
| `template:board:column:dropdown`                          | Dropdown menu in board columns                     |
| `template:board:column:header`                            | Board column header                                |
| `template:board:tooltip:subtasks:header:before-assignee`  | Header of Subtask table on tootip before Assignee  |
| `template:board:tooltip:subtasks:rows`                    | Column on row of Subtask table on tooltip          |
| `template:config:sidebar`                                 | Sidebar on settings page                           |
| `template:config:application `                            | Application settings form                          |
| `template:config:email`                                   | Email settings page                                |
| `template:config:integrations`                            | Integration page in global settings                |
| `template:dashboard:show`                                 | Main page of the dashboard                         |
| `template:dashboard:page-header:menu`                     | Dashboard submenu                                  |
| `template:header:dropdown`                                | Page header dropdown menu (user avatar icon)       |
| `template:header:creation-dropdown`                       | Page header dropdown menu (plus icon)              |
| `template:layout:head`                                    | Page layout `<head/>` tag                          |
| `template:layout:top`                                     | Page layout top header                             |
| `template:layout:bottom`                                  | Page layout footer                                 |
| `template:project:dropdown`                               | "Actions" menu on left in different project views  |
| `template:project:header:before`                          | Project filters (before)                           |
| `template:project:header:after`                           | Project filters (after)                            |
| `template:project:integrations`                           | Integration page in projects settings              |
| `template:project:sidebar`                                | Sidebar in project settings                        |
| `template:project-user:sidebar`                           | Sidebar on project user overview page              |
| `template:project-list:menu:before`                       | Project list: before menu entries                  |
| `template:project-list:menu:after`                        | Project list: after menu entries                   |
| `template:project-overview:before-description`            | Project overview: before description               |
| `template:project-header:view-switcher`                   | Project view switcher                              |
| `template:task:layout:top`                                | Task layout top (after page header)                |
| `template:task:details:top`                               | Task summary top                                   |
| `template:task:details:bottom`                            | Task summary bottom                                |
| `template:task:details:first-column`                      | Task summary first column                          |
| `template:task:details:second-column`                     | Task summary second column                         |
| `template:task:details:third-column`                      | Task summary third column                          |
| `template:task:details:fourth-column`                     | Task summary fourth column                         |
| `template:task:dropdown`                                  | Task dropdown menu in listing pages                |
| `template:task:sidebar:actions`                           | Sidebar on task page (section actions)             |
| `template:task:sidebar:information`                       | Sidebar on task page (section information)         |
| `template:task:form:first-column`                         | 1st column in task form                            |
| `template:task:form:second-column`                        | 2nd column in task form                            |
| `template:task:form:third-column`                         | 3nd column in task form                            |
| `template:task:show:top   `                               | Show task page: top                                |
| `template:task:show:bottom`                               | Show task page: bottom                             |
| `template:task:show:before-description`                   | Show task page: before description                 |
| `template:task:show:before-tasklinks`                     | Show task page: before tasklinks                   |
| `template:task:show:before-subtasks`                      | Show task page: before subtasks                    |
| `template:task:show:before-timetracking`                  | Show task page: before timetracking                |
| `template:task:show:before-attachments`                   | Show task page: before attachments                 |
| `template:task:show:before-comments`                      | Show task page: before comments                    |
| `template:subtask:form:create`                            | "Create Subtask" form                              |
| `template:subtask:form:edit`                              | "Edit Subtask" form                                |
| `template:subtask:table:header:before-timetracking`       | Subtask table header before Time Tracking          |
| `template:subtask:table:rows`                             | Column on row of subtasks table                    |
| `template:user:authentication:form`                       | "Edit authentication" form in user profile         |
| `template:user:create-remote:form`                        | "Create remote user" form                          |
| `template:user:external`                                  | "External authentication" page in user profile     |
| `template:user:integrations`                              | Integration page in user profile                   |
| `template:user:sidebar:actions`                           | Sidebar in user profile (section actions)          |
| `template:user:sidebar:information`                       | Sidebar in user profile (section information)      |
| `template:user:show:profile:info`                         | User profile information                           |


Another template hooks can be added if necessary, just ask on the issue tracker.
