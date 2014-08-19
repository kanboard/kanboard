Board configuration
===================

Some parameters for the boards can be changed with a config file.

Default values are available in the file `config.default.php`.
If you want to override the default values, you have to create a config file `config.php` in the root directory of your Kanboard installation.

### Auto-refresh frequency for the public board view

```php
// Auto-refresh frequency in seconds for the public board view (60 seconds by default)
define('BOARD_PUBLIC_CHECK_INTERVAL', 60);
```

### Auto-refresh frequency for the board (Ajax polling)

```php
// Board refresh frequency in seconds (the value 0 disable this feature, 10 seconds by default)
define('BOARD_CHECK_INTERVAL', 10);
```

### Task highlighting

Display a shadow around the task when a task was moved recently. Set the value 0 to disable this feature.

```php
// Period (in seconds) to consider a task was modified recently (0 to disable, 2 days by default)
define('RECENT_TASK_PERIOD', 48*60*60);
```
