#!/bin/sh

VERSION=$1
APP="kanboard"

cd /tmp
rm -rf /tmp/$APP /tmp/$APP-*.zip 2>/dev/null
git clone git@github.com:fguillot/$APP.git
rm -rf $APP/data/*.sqlite $APP/.git $APP/.gitignore $APP/scripts $APP/examples
sed -i.bak s/master/$VERSION/g $APP/models/base.php && rm -f $APP/models/*.bak
zip -r $APP-$VERSION.zip $APP
mv $APP-*.zip ~/Devel/websites/$APP
rm -rf /tmp/$APP 2>/dev/null

