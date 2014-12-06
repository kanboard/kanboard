#!/bin/sh

if [ "$#" -lt 1 ]
then
    echo "Usage: $0 <version> [destination]"
    exit 1
fi

APP="kanboard"
VERSION=$1
DESTINATION=$2

if [ -z "$2" ]
then
    DESTINATION=~/Devel/websites/$APP
fi

echo "Build package for version $VERSION => $DESTINATION"

# Cleanup
rm -rf /tmp/$APP /tmp/$APP-*.zip 2>/dev/null

# Download source code
cd /tmp
git clone --depth 1 -q https://github.com/fguillot/$APP.git >/dev/null

# Install vendors
cd /tmp/$APP
composer --prefer-dist --no-dev --optimize-autoloader --quiet install

# Remove useless files
rm -rf data/*.sqlite \
       .git \
       .gitignore \
       scripts \
       tests \
       Vagrantfile \
       .*.yml \
       README.markdown \
       docs \
       Dockerfile \
       composer.*

# Set the version number
sed -i.bak s/master/$VERSION/g app/constants.php && rm -f app/*.bak

# Make the archive
cd /tmp
zip -r $APP-$VERSION.zip $APP > /dev/null
mv $APP-$VERSION.zip $DESTINATION

cd $DESTINATION

# Make symlink for generic archive
if [ -L $APP-latest.zip ]
then
    unlink $APP-latest.zip
    ln -s $APP-$VERSION.zip $APP-latest.zip
fi

rm -rf /tmp/$APP 2>/dev/null
