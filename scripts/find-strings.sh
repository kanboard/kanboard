#!/bin/bash
#find . -name '*.php' -print | xargs -n 1 cat | grep -oP -e "\b(e|t)\([\"\']\K.*?[\"\'] *[\)\,]" | sed -e "s/'[),]$//" | sort | uniq
