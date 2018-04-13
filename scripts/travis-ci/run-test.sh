#!/bin/bash

# Run either Behat or PHP_CodeSniffer tests on Travis CI, depending on the
# passed in parameter.

case "$1" in
    PHP_CodeSniffer)
        ./vendor/bin/phpcs -p --standard=PSR2 src/
        exit $?
        ;;
    *)
        ./vendor/bin/behat
        exit $?
esac
