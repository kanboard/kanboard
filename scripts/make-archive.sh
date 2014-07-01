#!/bin/sh

VERSION=$1
APP="kanboard"

cd /tmp
rm -rf /tmp/$APP /tmp/$APP-*.zip 2>/dev/null
git clone https://github.com/fguillot/$APP.git
rm -rf $APP/data/*.sqlite $APP/.git $APP/.gitignore $APP/scripts $APP/tests $APP/Vagrantfile $APP/.*.yml $APP/phpunit.xml $APP/README.markdown $APP/docs
sed -i.bak s/master/$VERSION/g $APP/app/common.php && rm -f $APP/app/*.bak
zip -r $APP-$VERSION.zip $APP
mv $APP-*.zip ~/Devel/websites/$APP
rm -rf /tmp/$APP 2>/dev/null
