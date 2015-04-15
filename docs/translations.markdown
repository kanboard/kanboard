Translations
============

How to translate Kanboard to a new language?
--------------------------------------------

- Translations are stored inside the directory `app/Locale`
- There is sub-directory for each language, by example for the French we have `fr_FR`, Italian `it_IT` etc...
- A translation is a PHP file that return an Array with a key-value pairs
- The key is the original text in english and the value is the translation for the corresponding language
- **French translations are always up to date**
- Always use the last version (branch master)

### Create a new translation:

1. Make a new directory: `app/Locale/xx_XX` by example `app/Locale/fr_CA` for French Canadian
2. Create a new file for the translation: `app/Locale/xx_XX/translations.php`
3. Use the content of the French locales and replace the values
4. Inside the file `app/Model/Config.php`, add a new entry for your translation inside the function `getLanguages()`
5. Check with your local installation of Kanboard if everything is ok
6. Send a [pull-request with Github](https://help.github.com/articles/using-pull-requests/)

How to update an existing translation?
--------------------------------------

1. Open the translation file `app/Locale/xx_XX/translations.php`
2. Missing translations are commented with `//` and the values are empty, just fill blank and remove the comment
3. Check with your local installation of Kanboard and send a [pull-request](https://help.github.com/articles/using-pull-requests/)

How to add new translated text in the application?
--------------------------------------------------

Translations are displayed with the following functions in the source code:

- `t()`: dispaly text with HTML escaping
- `e()`: display text without HTML escaping
- `dt()`: display date and time using the `strftime()` function formats

Always use the english version in the source code.

### Date and time translation

Date strings use the function `strftime()` to format the date.

By example, the original English version can be defined like that `Created on %B %e, %Y at %k:%M %p` and that will output something like that `Created on January 11, 2015 at 15:19 PM`. The French version can be modified to display a different format, `Créé le %d/%m/%Y à %H:%M` and the result will be `Créé le 11/01/2015 à 15:19`.

All formats are available in the [PHP documentation](http://php.net/strftime).

### Placeholders

Text strings use the function `sprintf()` to replace elements:

- `%s` is used to replace a string
- `%d` is used to replace an integer

All formats are available in the [PHP documentation](http://php.net/sprintf).

How to find missing translations in the applications?
-----------------------------------------------------

From a Unix shell run:

```bash
./scripts/find-strings.sh
```

All missing translations are displayed on the screen. Put that in the french locale and sync other locales (see below).

How to synchronize translation files?
-------------------------------------

From a Unix shell run this command:

```bash
./scripts/sync-locales.php
```

The french translation is used a reference for other locales.
