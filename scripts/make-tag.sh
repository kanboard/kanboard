#!/bin/sh

VERSION=$1

git tag -a v$VERSION -m "Version $VERSION"
git push origin v$VERSION
