Authentification à deux facteurs
=========================

Chaque utilisateur peut activer [l'authentification à deux facteurs](http://en.wikipedia.org/wiki/Two_factor_authentication).
Après s’être connecté, un code à usage unique (6 caractères) est demandé à l'utilisateur pour lui autoriser l’accès à Kanboard.

Ce code doit être fourni par un logiciel compatible, généralement installé sur votre smartphone.

Kanboard utilise le [Time-based One-time Password Algorithm](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) défini dans la [RFC 6238](http://tools.ietf.org/html/rfc6238).

Il existe de nombreux logiciels compatibles avec le standard TOTP system.
Par exemple, vous pouvez utilisez ces applications libres et open source :

- [Google Authenticator](https://github.com/google/google-authenticator/) (Android, iOS, Blackberry)
- [FreeOTP](https://fedorahosted.org/freeotp/) (Android, iOS)
- [OATH Toolkit](http://www.nongnu.org/oath-toolkit/) (utilitaire en ligne de commande sur Unix/Linux)

Ce système peut fonctionner hors ligne et vous n'avez pas l'obligation d'avoir un téléphone portable.

Paramétrage
-----

1. Allez dans le profil utilisateur.
2. Sur la gauche, cliquez sur **Authentification à deux facteurs** et cochez la case.
3. Une clef secrète est générée pour vous.

![2FA](screenshots/2fa.png)

- Vous devez sauvegarder votre clef dans votre logiciel TOTP. Si vous utilisez un smartphone, la solution la plus simple est de scanner le QR code avec FreeOTP ou Google Authenticator
- À chaque ouverture de session, un nouveau code sera demandé
- N'oubliez pas de tester votre appareil avant de quitter votre session

Une nouvelle clef est générée à chaque fois que vous activez/désactivez cette fonction
