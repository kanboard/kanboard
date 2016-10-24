Mise à jour de Kanboard à une nouvelle version
==============================================

La plupart du temps, mettre à jour Kanboard vers une nouvelle version est transparent.
Le processus pourrait se résumer à simplement copier le dossier `data` vers le nouveau répertoire `kanboard`.
Kanboard va appliquer les migrations SQL automatiquement pour vous.

Choses importantes à faire avant la mise à jour
-----------------------------------------------

- **Toujours, faire une sauvegarde complète de vos données avant !**
- **Vérifiez que votre sauvegarde est valide !**
- Vérifiez encore
- Toujours lire la [liste des changements](https://github.com/kanboard/kanboard/blob/master/ChangeLog) pour vérifier sil y a des opérations manuelles à faire
- Toujours fermer les sessions des utilisateurs sur le serveur

Depuis l'archive (version stable)
---------------------------------

1. Décompressez la nouvelle archive
2. Copier le dossier `data` dans le nouveau répertoire décompressé
3. Copiez votre fichier de configuration personnalisé `config.php` si vous en avez un
4. Si vous avez installé des plug-ins, utilisez la dernière version
5. Vérifiez que le répertoire `data` est accessible en écriture par l'utilisateur du serveur web
6. Testez que tout fonctionne correctement
7. Supprimez l'ancien répertoire de Kanboard

Depuis le dépôt git (version de développement)
---------------------------------------------

1. `git pull`
2. `composer install --no-dev`
3. Testez que tout fonctionne correctement

Cette méthode va installer **la version en cours de développement**, utilisez là à vos risques.
