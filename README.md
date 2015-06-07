iProducer
=========

This web-application is build with the Anax-MVC framework for the final project in the PHPMVC course, this application is discussion forum for music producers.

Installation Guide
----------------------------------------

Start off my cloning this repo or downloading the .zip. Next you need to make sure that the application is authorized to write to the database file by giving the database location chmod 777. The database is located in webroot/database.

This application uses SQLite so you will need to have SQLite installed on your computer in order to run this application.

You might need to make some changes in the .htaccess file to make the application work locally. 

```
RewriteBase /~sehu14/phpmvc/kmom10/webroot/
```
to:
```
# RewriteBase /~sehu14/phpmvc/kmom10/webroot/
``
