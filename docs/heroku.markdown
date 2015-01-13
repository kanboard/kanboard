Deploy Kanboard on Heroku
=========================

You can try Kanboard for free on [Heroku](https://www.heroku.com/).
You can use this one click install button or follow the manual instructions below:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy?template=https://github.com/fguillot/kanboard)

Requirements
------------

- Heroku account, you can use a free account
- Heroku command line tool installed

Manual instructions
-------------------

```bash
# Get the last development version
git clone https://github.com/fguillot/kanboard.git
cd kanboard

# Push the code to Heroku (You can also use SSH if git over HTTP doesn't work)
heroku create
git push heroku master

# Start a new dyno with a Postgresql database
heroku ps:scale web=1
heroku addons:add heroku-postgresql:hobby-dev

# Open your browser
heroku open
```

Limitations
-----------

The storage on Heroku is ephemeral, that means uploaded files through Kanboard are not persistent after a reboot.
