Actions automatiques
====================

Pour réduire au minimum l'interaction avec les utilisateurs, Kanboard dispose d'actions automatiques.

Chaque action automatique est définie ainsi :

- Un événement à suivre
- Une action associée à cet évènement
- Éventuellement quelques paramètres à définir

Chaque projet a une série d'actions automatisées qui lui sont propres, le panneau de configuration est situé sur la page qui liste les projets, il vous suffit de cliquer sur le lien **Actions automatiques**.

Ajouter une nouvelle action
---------------------------

Cliquez sur le lien **Ajouter une nouvelle action**.

![Action automatique](screenshots/automatic-action-creation.png)

- Commencez par choisir une action
- Ensuite, sélectionnez un évènement
- Et pour finir, les paramètres de l'action

Liste des évènements disponibles
--------------------------------

- Déplacement d'une tâche vers une autre colonne
- Déplacement d'une tâche à un autre emplacement de la même colonne
- Modification d'une tâche
- Création d'une tâche
- Réouverture d'une tâche
- Fermeture d'une tâche
- Création ou modification d'une tâche
- Changement d'assigné à une tâche
- Création ou mise à jour du lien vers une tâche
- Réception d'un *commit* de Github
- Ouverture d'une *issue* de Github
- Fermeture d'une *issue* de Github
- Réouverture d'une *issue* de Github
- Modification de l'assigné à une *issue* de Github
- Modification de l'étiquette d'une *issue* de Github
- Création d'un commentaire d'une *issue* de Github
- Ouverture d'une *issue* de Gitlab
- Fermeture d'une *issue* de Gitlab
- Réception d'un *commit* de Gitlab
- Réception d'un *commit* de Bitbucket
- Ouverture d'une *issue* de Bitbucket
- Fermeture d'une *issue* de Bitbucket
- Réouverture d'une *issue* de Bitbucket
- Modification de l'assigné à une *issue* de Bitbucket issue assignee change
- Création d'un commentaire d'une *issue* de Bitbucket

Liste des actions disponibles
-----------------------------

- Fermer une tâche
- Ouvrir une tâche
- Assigner la tâche à un utilisateur particulier
- Assigner la tâche à la personne qui fait l'action
- Cloner la tâche depuis un autre projet
- Déplacer la tâche vers un autre projet
- Déplacer la tâche vers une autre colonne quand elle est assignée à un utilisateur
- Déplacer la tâche vers une autre colonne quand quand l'assigné est supprimé
- Assigner une couleur quand la tâche est déplacée vers une colonne particulière
- Assigner une couleur à un utilisateur particulier
- Assigner automatiquement une couleur selon la catégorie
- Assigner automatiquement une catégorie en fonction d'une couleur
- Créer un commentaire depuis un fournisseur externe
- Créer une tâche depuis un fournisseur externe
- Ajouter un journal de commentaires quand on change une tâche de colonne
- Modifier l'assigné en fonction d'un nom d'utilisateur externe
- Modifier la catégorie en fonction d'une étiquette externe
- Mettre à jour automatiquement la date de début
- Déplacer la tâche vers une autre colonne quand la catégorie a changé
- Envoyer une tâche par mail à quelqu'un
- Modifier la couleur de la tâche quand on utilise un lien particulier pour cette tâche

Exemples
--------
Voici quelques exemples d'utilisation dans la vraie vie :

### Quand je déplace une tâche vers la colonne "Terminer", fermer automatiquement cette tâche

- Choisir l'action : **Fermer la tâche**
- Choisir l'évènement : **Déplacement d'une tâche vers une autre colonne**
- Définir le paramètre de l'action : **Colonne = Terminé** (c'est la colonne de destination)

### Quand je déplace une tâche vers la colonne "À valider", assigner cette tâche à un utilisateur particulier

- Choisir l'action : **Assigner la tâche à un utilisateur particulier**
- Choisir l'évènement :  **Déplacer une tâche vers une nouvelle colonne**
- Définir les paramètres de l'action :**Colonne = À valider** et **Utilisateur = Adrien** (Adrien est par exemple un testeur)

### Quand je déplace une tâche vers la colonne "Travail en cours", assigner cette tâche à l'utilisateur courant

- Choisir l'action : **Assigner la tâche à la personne qui fait cette action**
- Choisir l'évènement :  **Déplacer une tâche vers une autre colonne**
- Définir le paramètre de l'action : **Colonne = Travail en cours**

### Quand une tâche est terminée, dupliquer cette tâche vers un autre projet

Supposons que nous ayons deux projets : "Commande du client" et "Production". Une fois validée la commande, la basculer vers le projet "Production".

- Choisir l'action : **Dupliquer la tâche vers un autre projet**
- Choisir l'évènement :  **Fermer une tâche**
- Définir les paramètres de l'action : **Colonne = Validé** et **Projet = Production**

### Quand une tâche est déplacée vers la toute dernière colonne, déplacer la même tâche exactement vers un autre projet

Supposons que nous ayons deux projets : "Idées" et "Développement". Une fois validée l'idée, la basculer vers le projet "Développement".

- Choisir l'action : **Déplacer la tâche vers un autre projet**
- Choisir l'évènement :  **Déplacer une tâche vers une autre colonne**
- Définir les paramètres de l'action : **Colonne = Validé** et **Projet = Développement**

### Je veux assigner automatiquement une couleur à l'utilisateur Adrien

- Choisir l'action : **Assigner une couleur à un utilisateur particulier**
- Choisir l'évènement :  **Modification de l'assigné à une tâche**
- Définir les paramètres de l'action :**Couleur = Vert** et **Assigné = Adrien**

### Je veux assigner automatiquement une couleur à la catégorie "Demande de fonctionnalité"

- Choisir l'action :  **Assigner automatiquement une couleur à une catégorie particulière**
- Choisir l'évènement :  **Création ou modification d'une tâche**
- Définir les paramètres de l'action : **Couleur = Bleu** et **Catégorie = Demande de fonctionnalité**

### Je veux régler automatiquement la date de début quand la tâche est déplacée dans la colonne "Travail en cours"

- Choisir l'action :  **Mettre à jour automatiquement la date de début**
- Choisir l'évènement :  **Déplacer une tâche vers une autre colonne**
- Définir les paramètres de l'action : **Colonne= Travail en cours**
