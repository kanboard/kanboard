Environment Variables
=====================

Environment variables maybe useful when Kanboard is deployed as container (Docker).

| Variable      | Description                                                                                                                     |
|---------------|---------------------------------------------------------------------------------------------------------------------------------|
| DATABASE_URL  | `[database type]://[username]:[password]@[host]:[port]/[database name]`, example: `postgres://foo:foo@myserver:5432/kanboard`   |
| DEBUG         | Enable/Disable debug mode                                                                                                       |
| DEBUG_FILE    | Debug file location, `DEBUG_FILE=php://stderr`                                                                                  |
| ENABLE_SYSLOG | Enable/Disable logging to Syslog: `ENABLE_SYSLOG=1`                                                                             |
