Heroku üzerinde Kanboard dağıtma
=========================

[Heroku](https://www.heroku.com/)  'da Kanboard'u ücretsiz deneyebilirsiniz.
Bu tek bir tıklama yükleme düğmesini kullanabilirsiniz veya aşağıdaki el-ile-manuel talimatları izleyin:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy?template=https://github.com/kanboard/kanboard)

Gereksinimler
------------

- Heroku hesabı, ücretsiz bir hesap kullanabilirsiniz
- Heroku komut satırı araçları yüklenmiş olmalı

Manuel talimatlar
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

Sınırlamalar
-----------

- Heroku'nun depolaması kısa ömürlüdür, bu da, bir yeniden başlatma sonrasında yüklenen dosyalar Kanboard'dan kalıcı değildir. Dosyalarınızı [Amazon S3](https://github.com/kanboard/plugin-s3) gibi bir bulut depolama sağlayıcısına depolamak için bir eklenti kurmak isteyebilirsiniz.
- Kanboard'un bazı özellikleri, [günlük arka plan işi](cronjob.markdown) çalıştırmanızı gerektirir.
