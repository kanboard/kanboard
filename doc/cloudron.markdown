How to run Kanboard on Cloudron
================================

[Cloudron](https://cloudron.io) is a private smartserver on which you can install web
apps like Kanboard. You can install Kanboard into a custom domain and each
installation is backed up and kept up-to-date with Kanboard releases automatically.

[![Install](https://cloudron.io/img/button.svg)](https://cloudron.io/button.html?app=net.kanboard.cloudronapp)

Accounts
--------

The app integrates tightly with the Cloudron User Management (via LDAP). Only
Cloudron users can access the Kanboard. In addition, any Cloudron administrator
becomes a Kanboard administrator automatically.

Installing Plugins
------------------

Plugins can be installed and configured using the [Cloudron CLI](https://git.cloudron.io/cloudron/cloudron-cli)
tool. See the [app description](https://cloudron.io/appstore.html?app=net.kanboard.cloudronapp) for
more information.

Application Source code
----------------------

The source code for the Cloudron app is [here](https://git.cloudron.io/cloudron/kanboard-app).

