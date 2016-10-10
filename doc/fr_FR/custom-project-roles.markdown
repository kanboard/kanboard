Rôles personnalisés pour les projets
====================================

Vous pouvez créer des rôles personnalisés pour les projets afin d'appliquer des restrictions spécifiques sur les personnes qui appartiennent à ce rôle.
Ces rôles personnalisés sont définis pour chaque projet.

Un rôle personnalisé hérite du rôle « Membre du projet ».
Par exemple, vous pouvez créer un rôle personnalisé pour forcer quelqu'un à suivre un process.
Vous pourriez avoir un groupe de gens qui sont autorisés seulement à déplacer des tâches entre les colonnes « Travail en cours » et « Terminé ».

Liste des restrictions
----------------------

- Restrictions au niveau du projet :
    - La création de tâches n'est pas permise
    - Ouvrir ou fermer une tâche n'est pas permise
    - Déplacer une tâche n'est pas autorisé
- Restrictions au niveau des colonnes :
    - La création de tâches est autorisée ou bloquée pour une colonne spécifique
    - L'ouverture ou la fermeture de tâche est autorisée ou bloquée pour une colonne spécifique
- Déplacer une tâche seulement entre les colonnes spécifiées

Configuration
-------------

### 1) Créer un rôle personnalisé

Depuis les réglages du projet, cliquez dans le menu à gauche sur **Rôles personnalisés** et en haut de la page sur **Ajouter un nouveau rôle personnalisé**.

![New custom role](screenshots/new_custom_role.png)

Donnez un nom au rôle et soumettez le formulaire.

### 2) Ajouter une restriction au rôle

Il y a plusieurs sortes de restrictions :

- Restrictions au niveau du projet
- Restriction sur le déplacement des tâches entre les colonnes
- Restrictions sur les colonnes

Vous pouvez cliquer sur le menu déroulant pour ajouter une nouvelle restriction :

![Ajouter une nouvelle restriction](screenshots/add_new_restriction.png)

### 3) Liste des restrictions

![Liste des restrictions](screenshots/example-restrictions.png)

Par exemple, ce rôle est capable de créer des tâches seulement dans la colonne « Backlog » et de déplacer des tâches entre les colonnes « Ready » et « Work in progress ».

### 4) Assigner le rôle à quelqu'un

Allez dans la section **Permissions** dans le menu sur la gauche et assignez le rôle personnalisé à l'utilisateur.

![Assignation du rôle](screenshots/custom_roles.png)

Exemples
--------

### Autoriser les gens à créer des tâches uniquement dans certaines colonnes

![Exemple de restriction sur la création des tâches](screenshots/example-restriction-task-creation.png)

- Les utilisateurs qui appartiennent à ce rôle seront capables de créer des tâches seulement dans la colonne « Backlog ».
- La combinaison des deux règles est importante, sinon cela ne fonctionnera pas.

### Autoriser les gens à changer le statut des tâches uniquement dans certaines colonnes

![Exemple de restriction sur statut des tâches](screenshots/example-restriction-task-status.png)

- Les utilisateurs qui appartiennent à ce rôle seront capables de change le statut des tâches seulement dans la colonne « Backlog ».
- Les tâches qui possèdent le statut ouvert sont visibles sur le tableau alors que celles qui ont le statut fermé ne sont pas visibles.

### Ne pas autoriser les gens à changer le statut des tâches dans une colonne spécifique

![Exemple de restriction sur les colonnes](screenshots/example-restriction-task-status-blocked.png)

Les utilisateurs qui appartiennent à ce rôle ne seront pas capables de changer le statut des tâches dans la colonne « Done ».
Par contre, cela reste possible dans les autres colonnes.

### Autoriser les gens à déplacer des tâches seulement entre certaines colonnes

![Exemple de restriction pour le drag and drop](screenshots/example-restriction-task-drag-and-drop.png)

Les utilisateurs qui appartiennent à ce rôle seront capables de déplacer les tâches seulement entre les colonnes « Ready » et « Work in progress ».
