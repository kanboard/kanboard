
Paramètres du projet
================

Aller dans le menu **Préférences**; puis choisissez **Paramètres du projet** sur la gauche

![Paramètres du projet](screenshots/project-settings.png)

###Colonnes par défaut pour les nouveaux projets

Vous pouvez changer le nom des colonnes par défaut.
C'est utile si vous créez toujours des projets comprenant les même colonnes

Chaque nom de colonne doit être séparé par une virgule.

Par défaut, Kanboard utilise les noms de colonne suivants : en attente, prêt, en cours, terminé.

###Catégories par défaut pour les nouveaux projets

Les catégories ne sont pas globales à l'application mais rattachées à un projet.
Chaque projet peut avoir plusieurs catégories.

De plus, si vous créez toujours la même catégorie pour tous vos projets, vous pouvez définir ici la liste des catégories à créer automatiquement

### Autoriser une seule sous-tâche en cours à la fois pour un utilisateur

Lorsque cette option est sélectionnée, un utilisateur ne peut travailler que sur une seule sous-tâche à la fois

Si une autre sous-tâche possède le statut « en cours », l'utilisateur verra cette boite de dialogue :
    
![Limite des sous-tâches pour l'utilisateur](screenshots/subtask-user-restriction.png)

### Déclencher automatiquement le suivi du temps pour les sous-tâches

- Si activé, lorsque le statut d'une sous-tâche devient « en cours », le chrono va démarrer automatiquement
- Désactivez cette option si vous n'utilisez pas le suivi du temps.

### Inclure les tâches fermées dans le diagramme de flux cumulé

- Si l'option est activée, les tâches fermées seront incluses dans le diagramme de flux cumulé
- Si l'option est désactivée, seules les tâches ouvertes seront incluses dans le diagramme de flux cumulé
- Cette option affecte la colonne "total" de la table "project_daily_column_stats"
