#!/bin/bash

REF_LANG=${1:-fr_FR}

###
APP_DIR=`dirname $0`/../app
LANG_FILE=$APP_DIR/Locale/$REF_LANG/translations.php
TMPFILE=`mktemp`

# find all strings used with t() or e() and write them to a temp buffer
find $APP_DIR -name '*.php' -print | xargs -n 1 cat | grep -oP -e "\b[et]\((\"\K.*?\"|\'\K.*?\') *[\)\,]" | sed -e "s/'[),]$//" -e 's/\\/\\\\/g' | sort | uniq > $TMPFILE

echo "Missing strings from $REF_LANG: (if none printed, none missing)"
while read LINE
do
    grep -F "$LINE" $LANG_FILE > /dev/null
    if [[ $? -ne 0 ]]; then
        echo "    '$LINE' => '',"
    fi
done < $TMPFILE

# delete the work file
rm $TMPFILE
