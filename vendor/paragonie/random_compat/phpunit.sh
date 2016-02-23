#!/usr/bin/env bash

if [ "$1" == 'full' ]; then
    fulltest=1
elif [ "$1" == 'each' ]; then
    testeach=1
else
    fulltest=0
fi

PHP_VERSION=$(php -r "echo PHP_VERSION_ID;")

echo
echo -e "\033[33mBegin Unit Testing\033[0m"
# Run the testing suite
echo "Basic test suite:"
php vendor/bin/phpunit tests/unit
if [ $? -ne 0 ]; then
    # Test failure
    exit 1
fi
echo "With open_basedir enabled:"
php -d open_basedir=`pwd` vendor/bin/phpunit tests/unit
if [ $? -ne 0 ]; then
    # Test failure
    exit 1
fi
echo "With open_basedir enabled, allowing /dev:"
php -d open_basedir=`pwd`:/dev vendor/bin/phpunit tests/unit
if [ $? -ne 0 ]; then
    # Test failure
    exit 1
fi
echo "With mbstring.func_overload enabled:"
php -d mbstring.func_overload=7 vendor/bin/phpunit tests/unit
if [ $? -ne 0 ]; then
    # Test failure
    exit 1
fi

if [[ "$testeach" == "1" ]]; then
    echo "    CAPICOM:"
    php vendor/bin/phpunit --bootstrap tests/specific/capicom.php tests/unit
    echo "    /dev/urandom:"
    php vendor/bin/phpunit --bootstrap tests/specific/dev_urandom.php tests/unit
    echo "    libsodium:"
    php vendor/bin/phpunit --bootstrap tests/specific/libsodium.php tests/unit
    echo "    mcrypt:"
    php vendor/bin/phpunit --bootstrap tests/specific/mcrypt.php tests/unit
    echo "    openssl:"
    php vendor/bin/phpunit --bootstrap tests/specific/openssl.php tests/unit
fi

# Should we perform full statistical analyses?
if [[ "$fulltest" == "1" ]]; then
    php vendor/bin/phpunit tests/full
    if [ $? -ne 0 ]; then
        # Test failure
        exit 1
    fi
fi

