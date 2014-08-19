Translations
============

How to translate Kanboard to a new language?
--------------------------------------------

- Translations are stored inside the directory `app/Locales`
- There is sub-directory for each language, by example for the French we have `fr_FR`, Italian `it_IT` etc...
- A translation is a PHP file that return an Array with a key-value pairs
- The key is the original text in english and the value is the translation for the corresponding language
- **French translations are always up to date**
- Always use the last version (branch master)

### Create a new translation:

1. Make a new directory: `app/Locales/xx_XX` by example `app/Locales/fr_CA` for French Canadian
2. Create a new file for the translation: `app/Locales/xx_XX/translations.php`
3. Use the content of the French locales and replace the values
4. Inside the file `app/Model/Config.php`, add a new entry for your translation inside the function `getLanguages()`
5. Check with your local installation of Kanboard if everything is ok
6. Send a pull-request with Github

How to update an existing translation?
--------------------------------------

1. Open the translation file `app/Locales/xx_XX/translations.php`
2. Missing translations are commented with `//` and the values are empty, just fill blank and remove comments
3. Check with your local installation of Kanboard and send a pull-request
