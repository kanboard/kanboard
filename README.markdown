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
- Translations in English and French

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
- Rzeka: https://github.com/rzeka

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

