worklog
=======

A Symfony project created on May 13, 2017, 5:00 pm.

Installation worklog:
1. Give appropriate permissions to the folders app/cache and app/logs (http://symfony.com/doc/2.6/book/installation.html)
2. Make dependencies with the command: composer install
3. Replace access data to the database MySQL in file app/config/parameters.yml
4. Create a database using the command: php app/console doctrine:database:create
5. Create a database schema with the command: php app/console doctrine:schema:update --force
6. Clean the application cache using the command: php app/console cache:clear
7. Install the assets with the command: php app/console assets:install

Todo:
1. to improve the mechanism (problem in case of multiple stoppages and resumption of the worklog)
2. commands should return nothing, for example, they can throw an exception, and an exception can be catch higher
3. separate commands
