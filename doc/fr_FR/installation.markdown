Installation
============

Avant toute chose, vérifiez-les [prérequis](requirements.markdown) avant d'aller plus loin.

Depuis l'archive (version stable)
---------------------------------

1. Vous devez avoir un serveur web avec PHP déjà configuré
2. Téléchargez le code source de l'application et copiez le dossier `kanboard` là où vous le souhaitez
3. Vérifiez si le répertoire `data` est accessible en écriture par l'utilisateur de votre serveur web
4. Avec votre navigateur, allez sur <http://yourpersonalserver/kanboard>
5. L'utilisateur et le mot de passe par défaut sont **admin/admin**
6. Commencez à utiliser le logiciel
7. N'oubliez pas de changer le mot de passe par défaut !

Le répertoire `data` est utilisé pour :

- La base de données Sqlite : `db.sqlite`
- Le fichier de débogage : `debug.log` (uniquement si le mode débug est actif)
- Les fichiers uploadés : `files/*`
- Les vignettes des images : `files/thumbnails/*`

Les gens qui utilisent une base de données distante (Mysql/Postgresql) ou un système de stockage distant tel que Amazon S3 n'ont pas forcément besoin d'avoir un dossier `data` local.

Depuis le dépôt git (version de développement)
----------------------------------------------

Vous devez installer [composer](https://getcomposer.org/) pour utiliser cette méthode.

1. `git clone https://github.com/kanboard/kanboard.git`
2. `composer install --no-dev`
3. Allez à l'étape 3) juste au-dessus

Cette méthode va installer **la version en cours de développement**, utilisez là à vos risques.

Installation en dehors du document root
---------------------------------------

Si vous souhaitez installer Kanboard en dehors du document root de votre serveur web, vous devez créer au minimum ces liens symboliques :

```bash
.
├── assets -> ../kanboard/assets
├── cli -> ../kanboard/cli
├── doc -> ../kanboard/doc
├── favicon.ico -> ../kanboard/favicon.ico
├── index.php -> ../kanboard/index.php
├── jsonrpc.php -> ../kanboard/jsonrpc.php
└── robots.txt -> ../kanboard/robots.txt
```

Le `.htaccess` est optionnel parce que sont contenu peut-être inclus directement dans la configuration Apache.

Vous pouvez également définir un autre dossier pour les plug-ins et les fichiers uploadés en changeant le [fichier de configuration](config.markdown).

Installations supplémentaires
-----------------------------

- Certaines fonctionnalités de Kanboard demandent à ce que vous installiez une [tâche planifiée](cronjob.markdown) (Rapports et statistiques)
- [Un processus qui tourne en arrière-plan](worker.markdown) peut être installé pour améliorer les performances

Sécurité
--------

- Ne pas oublier de changer le mot de passe par défaut
- Ne pas autoriser tout le monde à accéder au dossier `data` depuis l'URL. Il y a déjà un `.htaccess` pour Apache et un fichier `web.config` pour IIS mais rien pour Nginx.
