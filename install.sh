composer -v > /dev/null 2>&1
COMPOSER_IS_INSTALLED=$?
if [[ $COMPOSER_IS_INSTALLED -ne 0 ]]; then
  curl -sS https://getcomposer.org/installer | php
fi

php composer.phar install
chmod +x app/console

./app/console --help
