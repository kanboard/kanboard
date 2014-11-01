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
- Translated in 14 languages (Brazilian, Chinese, Danish, English, Finnish, French, German, Italian, Japanese, Polish, Russian, Spanish, Swedish, Thai)

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

- [What is Kanban?](docs/what-is-kanban.markdown)
- [Kanban vs Todo Lists and Scrum](docs/kanban-vs-todo-and-scrum.markdown)
- [Usage examples](docs/usage-examples.markdown)

#### Working with projects

- [Creating projects](docs/creating-projects.markdown)
- [Editing projects](docs/editing-projects.markdown)
- [Sharing boards and tasks](docs/sharing-projects.markdown)
- [Automatic actions](docs/automatic-actions.markdown)
- [Project permissions](docs/project-permissions.markdown)

#### Working with tasks

- [Creating tasks](docs/creating-tasks.markdown)

#### Working with users

- [User management](docs/user-management.markdown)

#### Settings

- [Application settings](docs/application-configuration.markdown)
- [Board settings](docs/board-configuration.markdown)

#### More

- [Syntax guide](docs/syntax-guide.markdown)
- [Frequently asked questions](docs/faq.markdown)

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

### Integration

- [Github webhooks](docs/github-webhooks.markdown)

#### Developers and sysadmins

- [Email configuration](docs/email-configuration.markdown)
- [Command line interface](docs/cli.markdown)
- [Json-RPC API](docs/api-json-rpc.markdown)
- [Webhooks](docs/webhooks.markdown)
- [Run Kanboard with Vagrant](docs/vagrant.markdown)
- [Run Kanboard with Docker](docs/docker.markdown)

### Contributors

- [Contributor guide](docs/contributing.markdown)
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
- [Aleix Pol](https://github.com/aleixpol)
- [Ashish Kulkarni](https://github.com/ashkulz)
- [Chorgroup](https://github.com/chorgroup)
- Claudio Lobo
- [Cmer](https://github.com/chncsu)
- [David-Norris](https://github.com/David-Norris)
- [Fengchao](https://github.com/fengchao)
- [Floaltvater](https://github.com/floaltvater)
- [Gavlepeter](https://github.com/gavlepeter)
- [Janne Mäntyharju](https://github.com/JanneMantyharju)
- [Jesusaplsoft](https://github.com/jesusaplsoft)
- [Kiswa](https://github.com/kiswa)
- [Kralo](https://github.com/kralo)
- [Lars Christian Schou](https://github.com/NegoZiatoR)
- [Levlaz](https://github.com/levlaz)
- [Lim Yuen Hoe](https://github.com/jasonmoofang)
- [Mathgl67](https://github.com/mathgl67)
- [Matthieu Keller](https://github.com/maggick)
- [Mauro Mariño](https://github.com/moromarino)
- [Maxime](https://github.com/EpocDotFr)
- [Moraxy](https://github.com/moraxy)
- [Nala Ginrut](https://github.com/NalaGinrut)
- [Nekohayo](https://github.com/nekohayo)
- [Nicolas Lœuillet](https://github.com/nicosomb)
- [Nramel](https://github.com/nramel)
- [Null-Kelvin](https://github.com/Null-Kelvin)
- [Oliver Bertuch](https://github.com/poikilotherm)
- [Olivier Maridat](https://github.com/oliviermaridat)
- [Rafaelrossa](https://github.com/rafaelrossa)
- [Raphaël Doursenaud](https://github.com/rdoursenaud)
- [Rzeka](https://github.com/rzeka)
- [Sebastien Pacilly](https://github.com/spacilly)
- [Sebastian Reese](https://github.com/ReeseSebastian)
- [Sylvain Veyrié](https://github.com/turb)
- [Toomyem](https://github.com/Toomyem)
- [Tony G. Bolaño](https://github.com/tonybolanyo)
- [Torsten](https://github.com/misterfu)
- [Troloo](https://github.com/troloo)
- [Typz](https://github.com/Typz)
- [Vedovator](https://github.com/vedovator)
- [Ybarc](https://github.com/ybarc)
- [Yuichi Murata](https://github.com/yuichi1004)

There is also many people who have reported bugs or proposed awesome ideas.
