Plugin Directory Configuration
==============================

To install, update and remove plugins from the user interface, you must have those requirements:

- The plugin directory must be writeable by the web server user
- The Zip extension must be available on your server
- The config parameter `PLUGIN_INSTALLER` must be set at `true`

To disable this feature, change the value of `PLUGIN_INSTALLER` to `false` in your config file.
You can also change the permissions of the plugin folder on the filesystem.

Only administrators are allowed to install plugins from the user interface.

By default, only plugin listed on Kanboard's website are available.
