Deploy Kanboard on Heroku
=========================

You can try Kanboard for free on [Heroku](https://www.heroku.com/).
You can use this one click install button or follow the manual instructions below:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy?template=https://github.com/kanboard/kanboard)

Requirements
------------

- Heroku account, you can use a free account
- Heroku command line tools installed

Manual instructions
-------------------

```bash
# Get the last development version
git clone https://github.com/kanboard/kanboard.git
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

- The storage of Heroku is ephemeral, that means uploaded files through Kanboard are not persistent after a restart. You may want to install a plugin to store your files in a cloud storage provider like [Amazon S3](https://github.com/kanboard/plugin-s3).
- Some features of Kanboard require that you run [a daily background job](cronjob.markdown).
