[ ![Codeship Status for ClevverMail/webapp](https://codeship.com/projects/3aef93f0-9a98-0133-1535-56295786b896/status?branch=develop)](https://codeship.com/projects/126498)

# WebApp

This repo holds the webapp for clevvermail.

## Deployment

The deployment is done by Codeship, a Continuous Integration SaaS, integrated with Capistrano as Continuous Deployment. This means, every time commits are pushed the CI runs the tests and depending on the branch the current state is deployed on the specified environment when the tests finsih successfully.

## Branch Overview

| Branch 	| Deployment to  	|
|---	|---	|
| master  	|  node{1,2}.eu.clevvermail.com 	|
| dev  	|  dev.eu.clevvermail.com 	|


## Setup

Be sure to install the following dependencies

```
apt-get install -y php5 libapache2-mod-php5 php5-mcrypt libapache2-mod-auth-mysql php5-mysql php5-curl imagemagick php5-imagick sendmail php5-gd php5-mysqlnd mpack ssmtp figlet update-motd
```

and to add the extension to php.ini by

```
echo "extension=php_curl.dll" >> /etc/php5/apache2/php.ini
echo "extension=php_openssl.dll" >> /etc/php5/apache2/php.ini
echo "extension=php_mysqli.dll" >> /etc/php5/apache2/php.ini
```

The following files need to be modified

```
system\virtualpost\config\{<environment>}\config.php
```

```
system\virtualpost\config\{<environment>}\database.php
```

You may also edit the tables *instance_domain*, *instance_database* as well as within the *settings* table the records 000114 within the database.


## Repo Structure

```
+-- assets
+-- config
|   +-- deploy
|   |   +-- production.rb
|   |   +-- staging.rb
|   +--deploy.rb
+-- database
+-- downloads
+-- images
+-- system
+-- uploads
```

## File Directories

There are 3 types of file storages:

1. PDF scanned files (alias resources):

```
/var/clevvermail/data
```

2. Asset files such as: images/css/js ... for showing web templates/themes:

```
.../system/virtualpost/themes/...
````

3. Uploaded files (images): 

```
uploads -> ../shared/uploads/
```

## DB Migration

for creating a database backup, run

```
mysqldump -u <db-user> -p <database_name> > backup.sql
```

Afterwards, move the sql file to the new system

```
scp backup.sql <ssh-user>@<new-host>:/home/<ssh-user>/
```

Then, you can import the sql again

```
mysql -u <new-db-user> -p <database_name> < backup.sql
```
