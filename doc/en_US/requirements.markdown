Requirements
============

Server side
-----------

### Compatible Operating Systems

| Operating System                     |
|--------------------------------------|
| Linux Ubuntu Xenial Xerus 16.04 LTS  |
| Linux Centos 7.x                     |
| Linux Redhat 7.x                     |
| Linux Debian 9                       |
| FreeBSD 10.x                         |
| Microsoft Windows 2016               |
| Microsoft Windows 2012 R2            |

### Compatible Databases

| Database           |
|--------------------|
| Sqlite >= 3.7      |
| Mysql >= 5.5       |
| MariaDB >= 10      |
| Postgresql >= 9.3  |

Which database to choose?

| Type            | Usage                                               |
|-----------------|-----------------------------------------------------|
| Sqlite          | Single user or small team (almost no concurrency)   |
| Mysql/Postgres  | Larger team, high-availability configuration        |

Do not use Sqlite on NFS mounts, only when you have a disk with fast I/O.

### Compatible Web Servers

| Web Server         |
|--------------------|
| Apache HTTP Server |
| Nginx              |
| Microsoft IIS      |
| Caddy Server       |

Kanboard is pre-configured to work with Apache (URL rewriting).

- Kanboard is NOT compatible with Apache `mod_security`.
- If you use Apache, you must have the module `mod_version`.

### PHP Versions

| PHP Version    |
|----------------|
| PHP >= 5.6.0   |

Since the version 1.2, Kanboard requires at least PHP 5.6.

### PHP Extensions Required

| PHP Extension              | Note                          |
|----------------------------|-------------------------------|
| pdo_sqlite                 | Only if you use Sqlite        |
| pdo_mysql                  | Only if you use Mysql/MariaDB |
| pdo_pgsql                  | Only if you use Postgres      |
| gd                         |                               |
| mbstring                   |                               |
| openssl                    |                               |
| json                       |                               |
| hash                       |                               |
| ctype                      |                               |
| session                    |                               |
| filter                     |                               |
| xml                        |                               |
| SimpleXML                  |                               |
| dom                        |                               |

### Optional PHP extensions

| PHP Extension              | Note                                       |
|----------------------------|--------------------------------------------|
| zip                        | Used to install plugins from Kanboard      |
| ldap                       | Only for LDAP authentication               |

### Recommendations

- Modern Linux or Unix operating system with the latest version of PHP.
- Best performances are obtained with the latest version of PHP with OpCode caching activated.

Client side
-----------

### Browsers

Always use a modern browser with the latest version if possible:

| Browser                               |
|---------------------------------------|
| Safari                                |
| Google Chrome                         |
| Mozilla Firefox                       |
| Microsoft Internet Explorer >= 11     |
| Microsoft Edge                        |

### Devices

| Device            | Screen resolution  |
|-------------------|--------------------|
| Laptop or desktop | >= 1366 x 768      |
| Tablet            | >= 1024 x 768      |
