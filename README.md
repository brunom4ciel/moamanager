# moamanager
MOAManager is an open-source Web tool that assists in the creation, execution, extraction, and edition of experiments in online environments using the MOA framework. 

Download repository

:~$ git clone https://github.com/brunom4ciel/moamanager/

Install MOAManager

:~$ sudo sh moamanager/setup/setup.sh

Or Update latest MOAManager

:~$ sudo sh moamanager/setup/update-latest.sh

If you find some MySQL/MariaDB commands are running without any password prompt want to see what password it is using behind the scenes, Debian stores the generated passwords in /etc/mysql/debian.cnf

/etc/mysql# cat debian.cnf 

# Automatically generated for Debian scripts. DO NOT TOUCH!
[client]
host     = localhost
user     = debian-sys-maint
password = <random string>
socket   = /var/run/mysqld/mysqld.sock
[mysql_upgrade]
host     = localhost
user     = debian-sys-maint
password = <random string>
socket   = /var/run/mysqld/mysqld.sock
basedir  = /usr
