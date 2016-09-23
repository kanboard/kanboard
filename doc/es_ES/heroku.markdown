Implementar Kanboard en Heroku
=========================

Usted puede tratar de forma gratuita en Kanboard[Heroku](https://www.heroku.com/).
Puede utilizar este botón de un solo clic instalar o siga las instrucciones del manual abajo:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy?template=https://github.com/kanboard/kanboard)

Requerimientos
------------
- Cuenta de Heroku, se puede utilizar una cuenta gratuita
- Herramientas de línea de comandos instalados Heroku

Manual de Instrucciones
-------------------

```bash
# Obtener la ultima version de desarrollo
git clone https://github.com/kanboard/kanboard.git
cd kanboard

# Empuje el código para Heroku (También puede utilizar SSH si Git sobre HTTP no funciona)
heroku create
git push heroku master

# Iniciar un nuevo banco de pruebas con una base de datos PostgreSQL
heroku ps:scale web=1
heroku addons:add heroku-postgresql:hobby-dev

# Abra su navegador
heroku open
```

Limitaciones
-----------

- El almacenamiento de Heroku es efímera, eso significa que los archivos cargados a través de Kanboard no son persistentes después de un reinicio. Es posible que desee instalar un plugin para almacenar sus archivos en un proveedor de almacenamiento en la nube como [Amazon S3](https://github.com/kanboard/plugin-s3).
- Algunas funciones de Kanboard requieren que ejecute [un trabajo en segundo plano todos los días] (cronjob.markdown).
