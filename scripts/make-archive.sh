#!/bin/sh

VERSION=$1
APP="kanboard"

cd /tmp
rm -rf /tmp/$APP /tmp/$APP-*.zip 2>/dev/null

git clone --depth 1 https://github.com/fguillot/$APP.git

rm -rf $APP/data/*.sqlite \
      $APP/.git $APP/.gitignore \
      $APP/scripts \
      $APP/tests \
      $APP/Vagrantfile \
      $APP/.*.yml \
      $APP/README.markdown \
      $APP/docs

sed -i.bak s/master/$VERSION/g $APP/app/constants.php && rm -f $APP/app/*.bak
zip -r $APP-$VERSION.zip $APP

mv $APP-$VERSION.zip ~/Devel/websites/$APP

cd ~/Devel/websites/$APP/
unlink $APP-latest.zip
ln -s $APP-$VERSION.zip $APP-latest.zip

rm -rf /tmp/$APP 2>/dev/null
