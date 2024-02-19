# Custom Hooks

Place custom git hooks in this directory and link them
into .git/hooks

Hooks have to be executable.

Alternatively run install-custom-hooks.sh which does it
for you.

Please also update the filelist in this script if you add
more hooks to this repo.

## post-checkout

This hook gets the current tag name or tag and revision
plus checked out commit hash and writes the result into
`app/version.txt`.
