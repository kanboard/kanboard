#!/bin/bash

css="base links title table form button alert tooltip header board project task comment subtask markdown listing activity dashboard pagination popover confirm sidebar responsive font-awesome.min jquery-ui-1.10.4.custom chosen.min"
js="jquery-1.11.1.min jquery-ui-1.10.4.custom.min jquery.ui.touch-punch.min chosen.jquery.min base board task analytic init"

rm -f assets/css/app.css
echo "/* DO NOT EDIT: auto-generated file */" > assets/css/app.css

for file in $css
do
    cat "assets/css/${file}.css" >> assets/css/app.css
done

rm -f assets/js/app.js
echo "/* DO NOT EDIT: auto-generated file */" > assets/js/app.js

for file in $js
do
    cat "assets/js/${file}.js" >> assets/js/app.js
done
