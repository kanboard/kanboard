Kanboard
========

Kanboard is a simple visual task board web application.

- Inspired by the [Kanban methodology](http://en.wikipedia.org/wiki/Kanban)
- Get a visual and clear overview of your project
- Multiple boards with the ability to drag and drop tasks
- Minimalist software, focus only on essential features (Less is more)
- Open source and self-hosted
- Super simple installation

Usage examples
--------------

You can customize your boards according to your business activities:

- Software management: Backlog, Ready, Work in Progress, To be tested, Validated
- Bug tracking: Received, Confirmed, Work in progress, Tested, Fixed
- Sales: Prospect, Meeting, Proposal, Sale
- Lean business management: Ideas, Developement, Measure, Analysis, Done
- Recruiting: Candidates Pool, Phone Screens, Job Interviews, Hires
- E-Commerce Shop: Orders, Packaged, Shipped
- Construction Planning: Materials ordered, Materials received, Work in progress, Work done, Invoice sent, Paid

Features
--------

- Multiple boards/projects
- Boards customization, rename or add columns
- Tasks with different colors, Markdown support for the description
- Users management with a basic privileges separation (administrator or regular user)
- Webhooks to create tasks from an external software
- Host anywhere (shared hosting, VPS, Raspberry Pi or localhost)
- No external dependencies
- **Super easy setup**, copy and paste files and you are done!
- Translations in English, French and Polish

Todo
----

- Touch devices support (tablets)
- Task search
- Task limit for each column
- File attachments
- Comments
- API
- Basic reporting
- Tasks export in CSV

Todo and known bugs
-------------------

- See Issues: <https://github.com/fguillot/kanboard/issues>

License
-------

- GNU Affero General Public License version 3: <http://www.gnu.org/licenses/agpl-3.0.txt>

Authors
-------

Original author: [Frédéric Guillot](http://fredericguillot.com/)

Contributors:

- Mathgl67: https://github.com/mathgl67
- Raphaël Doursenaud: https://github.com/rdoursenaud
- Rzeka: https://github.com/rzeka

There is also many people who have reported bugs or proposed awesome ideas.

Requirements
------------

- Apache or Nginx
- PHP >= 5.3.3
- PHP extensions required: mbstring and pdo_sqlite
- A web browser with HTML5 drag and drop support

Installation
------------

From the archive:

1. You must have a web server with PHP installed
2. Download the source code and copy the directory `kanboard` where you want
3. Check if the directory `data` is writeable (Kanboard stores everything inside a Sqlite database)
4. With your browser go to <http://yourpersonalserver/kanboard>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

From the repository:

1. `git clone https://github.com/fguillot/kanboard.git`
2. Go to the third step just above

Update
------

From the archive:

1. Close your session (logout)
2. Rename your actual Kanboard directory (to keep a backup)
3. Uncompress the new archive and copy your database file `db.sqlite` in the directory `data`
4. Make the directory `data` writeable by the web server user
5. Login and check if everything is ok
6. Remove the old Kanboard directory

From the repository:

1. Close your session (logout)
2. `git pull`
3. Login and check if everything is ok

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache but nothing for Nginx.

FAQ
---

### Which web browsers are supported?

Desktop version of Mozilla Firefox, Safari and Google Chrome.

### Why the minimum requirement is PHP 5.3.3 or 5.3.7?

Kanboard use the function `password_hash()` to crypt passwords but it's available only for PHP >= 5.5.
However, there is a backport for [older versions of PHP](https://github.com/ircmaxell/password_compat#requirements).
This library needs to have at least PHP 5.3.7 to work correctly (however on Debian Wheezy, PHP 5.3.3 should be fine).

### How to test Kanboard with Vagrant?

- Install Vagrant (http://www.vagrantup.com or apt-get install vagrant)
- Install VirtualBox (https://www.virtualbox.org/ or apt-get install virtualbox)
- Inside the root directory, run: vagrant up
- Go to http://localhost:8080/index.php
- Login with admin / admin

### How to test Kanboard with the PHP built-in web server?

If you don't want to install a web server like Apache on localhost. You can test with the embedded web server of PHP:

```bash
unzip kanboard-VERSION.zip
cd kanboard
php -S localhost:8000
open http://localhost:8000/
```

### How to install Kanboard on Debian?

```bash
apt-get update
apt-get install -y php5 php5-sqlite
cd /var/www/
wget http://kanboard.net/kanboard-VERSION.zip
unzip kanboard-VERSION.zip
chown -R www-data kanboard/data
```

### How to use the webhook to create a task?

Firstly, you have to get the token from the preferences page. After that, just call this url from anywhere:

```bash
# Create a task for the default project inside the first column
curl "http://myserver/?controller=task&action=add&token=superSecretToken&title=mySuperTask"

# Create a task to another project inside a specific column with the color red
curl "http://myserver/?controller=task&action=add&token=superSecretToken&title=task123&project_id=3&column_id=7&color_id=red"
```

Webhooks are useful to perform actions from external applications (shell-script, git hooks...).