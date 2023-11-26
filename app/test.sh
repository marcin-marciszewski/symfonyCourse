#!/bin/bash

if [ "$2" == "-db" ]
then
echo "rebuilding database ..."
php bin/console doctrine:schema:drop -n -q --force --full-database 
rm ./migrations/*.php 
bin/console make:migration 
bin/console doctrine:migrations:migrate -n -q 
bin/console doctrine:fixtures:load -n -q
fi

if [ -n "$1" ]
then
./bin/phpunit $1
else
./bin/phpunit
fi