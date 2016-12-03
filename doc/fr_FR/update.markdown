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
- Stoppez le _worker_
- Mettez le serveur web en mode maintenance pour éviter que les gens utilisent l'application pendant la mise à jour

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

Appliquer les migrations SQL manuellement
-----------------------------------------

Par défaut, les migrations SQL sont exécutées automatiquement.
La version du schéma est vérifiée à chaque requête.
De cette manière, les changements de base de données sont appliqués automatiquement.

Vous pouvez désactiver ce comportement si vous le souhaitez en fonction de votre configuration.
Par exemple, si plusieurs processus essaient de mettre à jour le schéma en même temps, il se peut que vous ayez des problèmes même si chaque opération se fait dans une transaction.

Pour désactiver cette fonctionnalité, mettez le paramètre `DB_RUN_MIGRATIONS` à `false` dans votre fichier de [configuration](config.markdown).

Lorsque vous allez mettre à jour Kanboard, exécutez cette commande :

```bash
./cli db:migrate
```
