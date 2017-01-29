Translations
============

How to translate Kanboard to a new language?
--------------------------------------------

- Translations are stored inside the directory `app/Locale`
- There is a subdirectory for each language, for example in French we have `fr_FR`, Italian `it_IT` etc.
- A translation is a PHP file that returns an Array with a key-value pairs
- The key is the original text in English and the value is the translation of the corresponding language
- **French translations are always up to date**
- Always use the last version (branch master)

### Create a new translation:

1. Make a new directory: `app/Locale/xx_XX` for example `app/Locale/fr_CA` for French Canadian
2. Create a new file for the translation: `app/Locale/xx_XX/translations.php`
3. Use the content of the French locales and replace the values
4. Update the file `app/Model/Language.php`
5. Check with your local installation of Kanboard if everything is OK
6. Send a [pull-request with Github](https://help.github.com/articles/using-pull-requests/)

How to update an existing translation?
--------------------------------------

1. Open the translation file `app/Locale/xx_XX/translations.php`
2. Missing translations are commented with `//` and the values are empty, just fill blank and remove the comment
3. Check with your local installation of Kanboard and send a [pull-request](https://help.github.com/articles/using-pull-requests/)

How to add new translated text in the application?
--------------------------------------------------

Translations are displayed with the following functions in the source code:

- `t()`: display text with HTML escaping
- `e()`: display text without HTML escaping

Always use the english version in the source code.

Text strings use the function `sprintf()` to replace elements:

- `%s` is used to replace a string
- `%d` is used to replace an integer

All formats are available in the [PHP documentation](http://php.net/sprintf).

How to find missing translations in the applications?
-----------------------------------------------------

From a terminal, run the following command:

```bash
./cli locale:compare
```

All missing and unused translations are displayed on the screen.
Put that in the French locale and sync other locales (see below).

How to synchronize translation files?
-------------------------------------

From a Unix shell run this command:

```bash
./cli locale:sync
```

The French translation is used a reference to other locales.
