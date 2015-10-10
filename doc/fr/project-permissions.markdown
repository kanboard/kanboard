Permissions des projets
===================

Deux sortes d'utilisateurs sont en charge d'un projet : les **gestionnaires de projet** et les **membres du projet**.

- Les gestionnaires de projet peuvent gérer la configuration du projet et accéder aux rapports.
- Les membres du projet ne peuvent effectuer que des opérations de base (créer ou déplacer des tâches).

Quand vous créez un nouveau projet, le statut de gestionnaire de projet vous est automatiquement attribué.

Les administrateurs de Kanboard peuvent accéder à tout mais ils ne sont pas nécessairement gestionnaires de projet ni membres du projet. **Ces permissions sont définies au niveau du projet**.

Permissions selon chaque rôle
-------------------------

### Membres du projet

- Utiliser le tableau (créer, déplacer et modifier les tâches)
- Supprimer seulement les tâches créées par eux-mêmes

### Gestionnaires du projet

- Utiliser le tableau 
- Configurer le projet
- Partager, renommer, dupliquer et désactiver le projet      
- Gérer les swimlanes, les catégories, colonnes et utilisateurs
- Modifier les actions automatisées
- Exporter en CSV
- Supprimer les tâches de n'importe quel membre du projet
- Accéder à la section analytique

Ils ne  **peuvent pas supprimer un projet**.

Gérer les utilisateurs et les permissions
----------------------------

Pour définir les rôles dans un projet, allez sur la page de  **configuration de projet** puis cliquez sur **Gestion des utilisateurs**.

### Gestion des utilisateurs

![Permissions du projet](http://kanboard.net/screenshots/documentation/project-permissions.png)

C'est l'endroit où vous pouvez choisir de nouveaux membres, modifier leur rôle ou interrompre l'accès d'un utilisateur.

### Permission générale

Si vous choisissez d'autoriser tout le monde (tous les utilisateurs de Kanboard), le projet est considéré comme public.

Cela signifie qu'il n'y a plus de rôle de gestionnaire de projet. Les permissions par utilisateur ne peuvent pas s'appliquer.
