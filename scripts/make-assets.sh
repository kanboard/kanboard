#!/bin/bash

css="base links title table form button alert tooltip header board project task comment subtask markdown listing activity dashboard pagination popover confirm sidebar responsive jquery-ui-1.10.4.custom chosen.min fullcalendar.min font-awesome.min"
js="jquery-1.11.1.min jquery-ui-1.10.4.custom.min jquery.ui.touch-punch.min chosen.jquery.min moment.min fullcalendar.min minify.min"
minify="base board calendar task analytic init"

rm -f assets/js/minify* 2>/dev/null
rm -f assets/js/app.js 2>/dev/null
rm -f assets/css/app.css 2>/dev/null

echo "/* DO NOT EDIT: auto-generated file */" > assets/css/app.css

# merge css
for file in $css
do
    cat "assets/css/${file}.css" >> assets/css/app.css
done

# minify
for file in $minify
do
    cat "assets/js/${file}.js" >> assets/js/minify.js
done

curl -s \
-d compilation_level=SIMPLE_OPTIMIZATIONS \
-d output_format=text \
-d output_info=compiled_code \
--data-urlencode "js_code@assets/js/minify.js" \
http://closure-compiler.appspot.com/compile > assets/js/minify.min.js

# concat app.js
for file in $js
do
    cat "assets/js/${file}.js" >> assets/js/app.js
done

rm -f assets/js/minify* 2>/dev/null
