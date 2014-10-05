Kanboard
========

Kanboard is a simple visual task board web application.

Official website: <http://kanboard.net>

- Inspired by the [Kanban methodology](http://en.wikipedia.org/wiki/Kanban)
- Get a visual and clear overview of your project
- Multiple boards with the ability to drag and drop tasks
- Minimalist software, focus only on essential features (Less is more)
- Open source and self-hosted
- Super simple installation

[![Build Status](https://travis-ci.org/fguillot/kanboard.svg)](https://travis-ci.org/fguillot/kanboard)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fguillot/kanboard/badges/quality-score.png?s=2b6490781608657cc8c43d02285bfafb4f489528)](https://scrutinizer-ci.com/g/fguillot/kanboard/)

Features
--------

- Multiple boards/projects
- Boards customization, rename/add/remove columns
- Tasks with different colors, categories, sub-tasks, attachments, comments and Markdown support for the description
- Automatic actions based on events
- User management with a basic privileges separation (administrator or regular user)
- Email notifications
- External authentication: Google, GitHub, LDAP/ActiveDirectory and Reverse-Proxy
- Webhooks to create tasks from an external software
- A basic command line interface
- Host anywhere (shared hosting, VPS, Raspberry Pi or localhost)
- No external dependencies
- **Super easy setup**, copy and paste files and you are done!
- Translations in English, French, Brazilian Portuguese, Spanish, German, Polish, Swedish, Finnish, Italian, Chinese, Russian...

Known bugs and feature requests
-------------------------------

See Issues: <https://github.com/fguillot/kanboard/issues>

License
-------

GNU Affero General Public License version 3: <http://www.gnu.org/licenses/agpl-3.0.txt>

Documentation
-------------

### Using Kanboard

#### Introduction

- [Usage examples](docs/usage-examples.markdown)

#### Working with projects

- [Creating projects](docs/creating-projects.markdown)
- [Editing projects](docs/editing-projects.markdown)
- [Sharing boards and tasks](docs/sharing-projects.markdown)
- [Automatic actions](docs/automatic-actions.markdown)

#### Working with tasks

- [Creating tasks](docs/creating-tasks.markdown)

#### Working with users

- [User management](docs/manage-users.markdown)

#### More

- [Syntax guide](docs/syntax-guide.markdown)

### Technical details

#### Installation

- [Installation instructions](docs/installation.markdown)
- [Upgrade Kanboard to a new version](docs/update.markdown)
- [Installation on Ubuntu](docs/ubuntu-installation.markdown)
- [Installation on Debian](docs/debian-installation.markdown)
- [Installation on Centos](docs/centos-installation.markdown)
- [Installation on Windows Server with IIS](docs/windows-iis-installation.markdown)
- [Example with Nginx + HTTPS + SPDY + PHP-FPM](docs/nginx-ssl-php-fpm.markdown)

#### Database

- [Sqlite database management](docs/sqlite-database.markdown)
- [How to use Mysql](docs/mysql-configuration.markdown)
- [How to use Postgresql](docs/postgresql-configuration.markdown)

#### Authentication

- [LDAP authentication](docs/ldap-authentication.markdown)
- [Google authentication](docs/google-authentication.markdown)
- [GitHub authentication](docs/github-authentication.markdown)
- [Reverse proxy authentication](docs/reverse-proxy-authentication.markdown)

#### Developers and sysadmins

- [Board configuration](docs/board-configuration.markdown)
- [Email configuration](docs/email-configuration.markdown)
- [Command line interface](docs/cli.markdown)
- [Json-RPC API](docs/api-json-rpc.markdown)
- [Webhooks](docs/webhooks.markdown)
- [How to use Kanboard with Vagrant](docs/vagrant.markdown)

### Contributors

- [Translations](docs/translations.markdown)
- [Coding standards](docs/coding-standards.markdown)
- [Running tests](docs/tests.markdown)

The documentation is written in [Markdown](http://en.wikipedia.org/wiki/Markdown).
If you want to improve the documentation, just send a pull-request.

FAQ
---

Go to the official website: <http://kanboard.net/faq>

Authors
-------

Original author: [Frédéric Guillot](http://fredericguillot.com/)

Contributors:

- Alex Butum
- Ashish Kulkarni: https://github.com/ashkulz
- Claudio Lobo
- Cmer: https://github.com/chncsu
- Floaltvater: https://github.com/floaltvater
- Gavlepeter: https://github.com/gavlepeter
- Janne Mäntyharju: https://github.com/JanneMantyharju
- Jesusaplsoft: https://github.com/jesusaplsoft
- Kiswa: https://github.com/kiswa
- Kralo: https://github.com/kralo
- Levlaz: https://github.com/levlaz
- Lim Yuen Hoe: https://github.com/jasonmoofang
- Mathgl67: https://github.com/mathgl67
- Matthieu Keller: https://github.com/maggick
- Mauro Mariño: https://github.com/moromarino
- Maxime: https://github.com/EpocDotFr
- Moraxy: https://github.com/moraxy
- Nala Ginrut: https://github.com/NalaGinrut
- Nekohayo: https://github.com/nekohayo
- Nramel: https://github.com/nramel
- Null-Kelvin: https://github.com/Null-Kelvin
- Olivier Maridat: https://github.com/oliviermaridat
- Poikilotherm: https://github.com/poikilotherm
- Rafaelrossa: https://github.com/rafaelrossa
- Raphaël Doursenaud: https://github.com/rdoursenaud
- Rzeka: https://github.com/rzeka
- Sebastien pacilly: https://github.com/spacilly
- Sylvain Veyrié: https://github.com/turb
- Toomyem: https://github.com/Toomyem
- Tony G. Bolaño: https://github.com/tonybolanyo
- Torsten: https://github.com/misterfu
- Troloo: https://github.com/troloo
- Typz: https://github.com/Typz
- Vedovator: https://github.com/vedovator
- Ybarc: https://github.com/ybarc

There is also many people who have reported bugs or proposed awesome ideas.
