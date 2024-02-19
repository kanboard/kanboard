#!/bin/bash
#
# Symlinks custom hooks from custom-hooks to .git/hooks

filelist="
post-checkout
"

for file in $filelist; do
	ln -s ../../custom-hooks/$file ../.git/hooks/$file
done

exit 0
