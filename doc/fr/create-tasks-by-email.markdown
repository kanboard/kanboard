Créer des tâches par email
=====================

Vous pouvez créer des tâches directement en envoyant un message.

Pour le moment, Kanboard fonctionne avec 3 services externes :

- [Mailgun](https://kanboard.net/documentation/mailgun)
- [Sendgrid](https://kanboard.net/documentation/sendgrid)
- [Postmark](https://kanboard.net/documentation/postmark)

Ces services gèrent le courrier entrant sans qu'on ait à configurer un serveur SMTP.

À la réception d'un email par l'un de ces services, le message qu'il contenait est transmis et traité automatiquement par Kanboard.
Toutes les opérations complexes sont prises en charge par ces services.

Processus de réception du courrier entrant
------------------------

1. Vous envoyez un mail à une adresse spécifique, par exemple **quelquechose+monprojet@inbound.mondomaine.tld**
2. Votre mail est envoyé sur les serveurs tiers SMTP
3. Le fournisseur de SMTP appelle Kanboard via un webhook avec le mail en JSON ou aux formats multipart/form-data
4. Kanboard analyse le mail reçu et crée la tâche dans le bon projet

Remarque : les nouvelles tâches sont automatiquement créées dans la première colonne.

Format du mail
------------

- La partie locale de l'adresse mail doit utiliser le signe + comme séparateur, par exemple **kanboard+projet123**
- La chaîne de caractères définie après le signe + doit correspondre à l'identifiant d'un projet, par exemple **projet123** est l'identifiant du projet **Projet 123**
- le sujet de l'email devient le titre de la tâche
- Le corps du message devient la description de la tâche (au format Markdown)

Les courriers entrants peuvent être écrits aux formats .txt ou .HTML.
**Kanboard peut convertir en Markdown les messages écrits en simple HTML**.

Sécurité et prérequis
-------------------------

- Le webhook de Kanboard est protégé par un jeton aléatoire
- L'adresse de l'expéditeur doit correspondre à celle d'un utilisateur de Kanboard
- L'utilisateur de Kanboard doit être un membre du projet
- Le projet Kanboard doit avoir un identifiant unique, par exemple **MONPROJET**

