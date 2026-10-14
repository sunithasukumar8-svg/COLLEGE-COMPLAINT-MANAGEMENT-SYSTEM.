cd /home/workspace/my-project

EXT=$(dirname $(find $PWD/phpext -name pdo_mysql.so))

php -d extension=$EXT/mysqlnd.so -d extension=$EXT/mysqli.so -d extension=$EXT/pdo_mysql.so -S 0.0.0.0:8080 -t public

https://62994be64c2f-0af40120-8080.ws6.app/# COLLEGE-COMPLAINT-MANAGEMENT-SYSTEM.
