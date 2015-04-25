#!/bin/bash

print_css="print links table board task comment subtask markdown"
app_css="base links title table form button alert tooltip header board task comment subtask markdown listing activity dashboard pagination popover confirm sidebar responsive dropdown"
vendor_css="jquery-ui.min chosen.min fullcalendar.min font-awesome.min"

app_js="base board calendar analytic swimlane dashboard budget screenshot"
vendor_js="jquery-1.11.1.min jquery-ui.min jquery.ui.touch-punch.min chosen.jquery.min dropit.min moment.min fullcalendar.min mousetrap.min mousetrap-global-bind.min app.min"
lang_js="da de es fi fr hu it ja nl pl pt-br ru sv sr th tr zh-cn"

function merge_css {

    local app_file="assets/css/app.css"
    local print_file="assets/css/print.css"

    rm -f $app_file 2>/dev/null
    rm -f $print_file 2>/dev/null
    echo "/* DO NOT EDIT: AUTO-GENERATED FILE */" > $app_file
    echo "/************************************/" >> $app_file
    echo "/* DO NOT EDIT: AUTO-GENERATED FILE */" > $print_file
    echo "/************************************/" >> $print_file

    for file in $vendor_css; do cat "assets/css/vendor/${file}.css" >> $app_file; done
    for file in $vendor_css; do cat "assets/css/vendor/${file}.css" >> $print_file; done

    for file in $app_css; do cat "assets/css/src/${file}.css" >> $app_file; done
    for file in $print_css; do cat "assets/css/src/${file}.css" >> $print_file; done
}

function minify_js {

    local tmp_file="assets/js/minify.js"
    local dst_file="assets/js/vendor/app.min.js"

    rm -f $dst_file $tmp_file 2>/dev/null

    for file in $app_js; do cat "assets/js/src/${file}.js" >> $tmp_file; done

    curl -s \
        -d compilation_level=SIMPLE_OPTIMIZATIONS \
        -d output_format=text \
        -d output_info=compiled_code \
        --data-urlencode "js_code@${tmp_file}" \
        http://closure-compiler.appspot.com/compile > $dst_file

    rm -f $tmp_file 2>/dev/null
}

function merge_js {

    local tmp_file="assets/js/vendor/app.min.js"
    local dst_file="assets/js/app.js"

    rm -f $dst_file 2>/dev/null

    for file in $vendor_js; do cat "assets/js/vendor/${file}.js" >> $dst_file; done
    for file in $lang_js; do cat "assets/js/vendor/lang/${file}.js" >> $dst_file; done

    rm -f $tmp_file 2>/dev/null
}

merge_css
minify_js
merge_js
