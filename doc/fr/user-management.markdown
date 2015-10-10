Gestion des utilisateurs
===============

Rôles au niveau de l'application
------------------------------

Kanboard utilise un système de permissions basique qui reconnaît 3 types d'utilisateurs :

### Administrateur

- Peut accéder à tout

### Administrateur de projets

- Peut créer des projets multi-utilisateurs et privés
- Peut convertir les projets multi-utilisateurs et privés
- Peut voir seulement ses propres projets
- Ne peut pas modifier les paramètres de l'application
- Ne peut pas gérer les utilisateurs

### Utilisateur standard

- Peut créer seulement des projets privés
- Peut voir seulement ses propres projets
- Ne peut pas supprimer de projets

Rôles au niveau des projets
--------------------------

Ces rôles sont liés aux permissions du projet.

### Gestionnaire de projet

- Peut gérer seulement ses propres projets
- Peut accéder aux rapports et à la section budget

### Membre du projet

- Peut pratiquer toutes les opérations quotidiennes sur son projet (créer et déplacer des tâches…)
- Ne peut pas configurer les projets

Remarque : n'importe quel « utilisateur de base » peut être promu « Gestionnaire de projet » pour un projet donné, il n'est pas nécessaire d'être « Administrateur de projets ».

Utilisateurs locaux et distants
----------------------

- Un utilisateur local est un compte qui utilise la base de données pour stocker ses identifiants. Les utilisateurs locaux utilisent le formulaire de connexion pour s'identifier.
- Un utilisateur distant est un compte qui utilise un système externe pour stocker ses identifiants. Par exemple, ce peut être un compte LDAP, Github ou Google. L'authentification de ces utilisateurs peut s'effectuer ou non avec le formulaire de connexion.

Ajouter un nouvel utilisateur
--------------

Pour ajouter un nouvel utilisateur, vous devez être administrateur.

1. Depuis le tableau de bord, allez au menu **Gestion des utilisateurs**
2. Dans la partie haute vous avez un lien **Créer un utilisateur local** ou **Créer un utilisateur distant**
3. Informez les champs de saisie et enregistrez

![Nouvel utilisateur](captures/kanboard-creer-utilisateur.png)

Quand vous créez un **utilisateur local**, vous devez préciser au moins deux valeurs :

- **nom d'utilisateur** : c'est l'identifiant unique de votre utilisateur (login)
- **mot de passe** : le mot de passe de votre utilisateur doit comporter au moins 6 caractères

Pour les **utilisateurs distants**, seul le nom d'utilisateur est obligatoire. Vous pouvez aussi leur associer leur compte Github ou Google si vous connaissez déjà leur id unique.

Modifier des utilisateurs
----------

Quand vous allez au menu **utilisateurs**, vous disposez d'une liste d'utilisateurs. Pour modifier un utilisateur cliquez sur le lien **Modifier**.

- si vous êtes un utilisateur ordinaire, vous ne pouvez modifier que votre propre profil
- vous devez être administrateur pour pouvoir modifier n'importe quel utilisateur

Supprimer des utilisateurs
------------

Depuis le menu **utilisateurs**, cliquez sur le lien **supprimer**. Ce lien n'est visible que si vous êtes administrateur.

Si vous supprimez un utilisateur particulier, **les tâches assignées à cette personne ne lui seront plus assignées** après cette opération.
