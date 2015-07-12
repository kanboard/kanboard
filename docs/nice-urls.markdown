URL rewriting
=============

Kanboard is able to work indifferently with url rewriting enabled or not.

- Example of URL rewritten: `/board/123`
- Otherwise: `?controller=board&action=show&project_id=123`

If you use Kanboard with Apache and with the mode rewrite enabled, nice urls will be used automatically.

URL Shortcuts
-------------

- Go to the task #123: **/t/123**
- Go to the board of the project #2: **/b/2**
- Go to the project calendar #5: **/c/5**
- Go to the list view of the project #8: **/l/8**
- Go to the project settings for the project id #42: **/p/42**

Configuration
-------------

By default, Kanboard will check if the Apache mode rewrite is enabled.

To avoid the automatic detection of url rewriting from the web server, you can enable this feature in your config file:

```
define('ENABLE_URL_REWRITE', true);
```

When this constant is at `true`:

- URLs generated from command line tools will be also converted
- If you use another web server than Apache, by example Nginx or Microsoft IIS, you have to configure yourself the url rewriting

Note: Kanboard always fallback to old school urls when it's not configured, this configuration is optional.
