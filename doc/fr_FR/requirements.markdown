Prérequis
=========

Côté serveur
------------

### Systèmes d'exploitation compatibles

| Système d'exploitation               |
|--------------------------------------|
| Linux Ubuntu Xenial Xerus 16.04 LTS  |
| Linux Ubuntu Trusty 14.04 LTS        |
| Linux Centos 6.x                     |
| Linux Centos 7.x                     |
| Linux Redhat 6.x                     |
| Linux Redhat 7.x                     |
| Linux Debian 8                       |
| FreeBSD 10.x                         |
| Microsoft Windows 2012 R2            |
| Microsoft Windows 2008               |

### Bases de données compatibles

| Base de données    |
|--------------------|
| Sqlite 3.x         |
| Mysql >= 5.5       |
| MariaDB >= 10      |
| Postgresql >= 9.3  |

Quelle base de données choisir ?

| Type            | Utilisation                                                 |
|-----------------|-------------------------------------------------------------|
| Sqlite          | Un seul utilisateur ou petite équipe (concurrence faible)   |
| Mysql/Postgres  | Équipe plus importante, installation à haute-disponibilité  |

Ne pas utiliser Sqlite sur des montages NFS, seulement lorsque vous avez un disque dur avec des entrées/sorties rapides.

### Serveurs web compatibles

| Serveur web        |
|--------------------|
| Apache HTTP Server |
| Nginx              |
| Microsoft IIS      |

Kanboard est préconfiguré pour fonctionner avec Apache (réécriture des URL).

### Versions de PHP compatibles

| Version de PHP |
|----------------|
| PHP >= 5.3.9   |
| PHP 5.4        |
| PHP 5.5        |
| PHP 5.6        |
| PHP 7.x        |

### Extensions PHP requises

| Extension PHP              | Note                                     |
|----------------------------|------------------------------------------|
| pdo_sqlite                 | Seulement si vous utilisez Sqlite        |
| pdo_mysql                  | Seulement si vous utilisez Mysql/MariaDB |
| pdo_pgsql                  | Seulement si vous utilisez Postgres      |
| gd                         |                                          |
| mbstring                   |                                          |
| openssl                    |                                          |
| json                       |                                          |
| hash                       |                                          |
| ctype                      |                                          |
| session                    |                                          |
| ldap                       | Seulement pour l'authentification LDAP   |
| Zend OPcache               | Recommandé                               |

### Extensions PHP optionnelles

| Extension PHP              | Note                                       |
|----------------------------|--------------------------------------------|
| zip                        | Utilisé pour installer les extensions      |

### Recommendations

- Système d'exploitation Unix ou Linux moderne.
- Les meilleures performances sont obtenues avec la dernière version de PHP et le cache OPcode activé.

Côté client
-----------

### Navigateurs web

Toujours utiliser un navigateur web moderne si possible :

| Navigateur web                        |
|---------------------------------------|
| Safari                                |
| Google Chrome                         |
| Mozilla Firefox                       |
| Microsoft Internet Explorer >= 11     |
| Microsoft Edge                        |

### Appareils

| Device            | Screen resolution  |
|-------------------|--------------------|
| Laptop or desktop | >= 1366 x 768      |
| Tablet            | >= 1024 x 768      |
