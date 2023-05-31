[![Build Status](https://github.com/softcoder/membership-signup/actions/workflows/php.yml/badge.svg)](https://github.com/softcoder/membership-signup/actions/workflows/php.yml)

To see detailed unit test and code coverage stats visit: https://github.com/softcoder/membership-signup/actions/workflows/php.yml

Membership-Signup
=================

<img src="https://github.com/softcoder/membership-signup/raw/master/screenshots/iconXX.png" align="left" >

A membership signup automation workflow
Current Version: 1.0.0

Description:

This application suite was designed by non-profit volunteers in order to help automate the process of club members signing up for club membership. For contact information see the contact section at the bottom of this page.

Key Features:
-------------
- Simple installation and setup (does not require a database)
- Automatic PDF document generation based on user input using template PDF documents with named fields.
- Automatic email verification and final PDF membership documents emailed to member as well as a membership director

Referral Codes:
---------------
If you are planning to purchase a Tesla product, please consider using my referral code:
https://ts.la/mark25940

Thank you!

Overview:
-------------------------

![Overview](/screenshots/membership-signup-diagram.png?raw=true "Overview")

The diagram above shows all of the possible features enabled and the communication paths involved.

Technology:
-------------------------
Membership Signup was written using PHP for its backend server operations and html5 + javascript for the frontend.

System Requirements:
--------------------
- An email account that can send emails to members
- The PDFtk tool (free for non commercial use) https://www.pdflabs.com/tools/pdftk-server/
- A webserver that can run PHP 8.x (such as Apache, IIS or NGinx)
- PDF documents with named fields acting as templates


Installation:
-------------
Getting started video - basic installation on Windows (click image below):

[![Getting Started install](http://img.youtube.com/vi/ZyUfvYsW39Q/0.jpg)](https://youtu.be/ZyUfvYsW39Q)

Getting started video - basic installation on Linux (click image below):

[![Ubuntu Host quick install](http://img.youtube.com/vi/PkREVKmyQzA/0.jpg)](https://youtu.be/PkREVKmyQzA)

[![Linux Host install](http://img.youtube.com/vi/ZDhPJ7qIXDc/0.jpg)](https://youtu.be/ZDhPJ7qIXDc)

General installation steps:
---------------------------
- Download the application either using git commands (for those who know how to use git) or download the master archive here: https://github.com/softcoder/membership-signup/archive/master.zip and extract to a folder on your local PC.
- Edit the values in [config-default.php](php/config-default.php) to suit your environment. (see Configuration section below)
- Rename the file config-default.php to config.php
- Upload the files in the php folder to a location on your webserver (this will be the root folder for membership-signup).
- If using IIS (Apache users skip to 'Open the url') you should import the file [IIS_Import.htaccess](php/IIS_Import.htaccess) following these steps:
-  1. Start IIS Manager. 
-  2. On the left, in the Connections pane, select 'Sites' then 'Default Web Site'.
-  3. Create a new virtual folder pointing to the root php folder of membership signup (example alias svvfd)
-  4. With the alias selected (example svvfd) click on the right, in Features View, IIS, click URL Rewrite.
-  5. On the right, in the Actions pane, click 'Open Feature'.
-  6. On the right, in the Actions pane, click 'Import Rules'.
-  7. Select the file IIS_import.htaccess using the ... elipses and import, then click apply.
- extract the contents of the appropriate third party archive into your membership signup root installation folder:
   ie: vendor-php-5.6.zip or vendor-php-X.X.zip (check for other supported versions where filename exists in repo)
- Open the url: http://www.yourwebserver.com/uploadlocation/install.php (substitute your root membership signup host://uploadpath/install.php)
- If everything was done correctly you should see an install page offering to install one the firehall's 
  you configured in config.php (we support more than 1 firehall if desired). Select the firehall and click install.
- If successful the installation will display the admin user's password. Click the link to login using it.

Linux installation notes:
-------------------------
1. Install LAMP (Linux, Apache, MySQL and PHP) apps
2. Install these dependencies:
- sudo a2enmod rewrite
- sudo apt install php7.1-xml
- sudo apt install php7.1-mysql 
- sudo apt install php7.1-imap
- sudo apt install php7.1-mcrypt
- sudo apt install php7.1-curl
- sudo apt install php7.1-ldap
- sudo apt install php7.1-sqlite3
3. Restart Apache: sudo systemctl restart apache2
4. Configure web virtual host (if desired)
5. Copy membership signup (PHP folder) to the appropriate folder on the target host

Configuration:
--------------

The most important information that you require to configure is located in config.php. 
You must create this file (or rename [config-default.php](php/config-default.php) to config.php) and supply configuration values.
The following explains the main sections in config.php. The structures used in coinfig.php are
defined in [config_interfaces.php](php/config_interfaces.php) if you are interested to see their definitions.


FAQ:
----
- For apache virtual host support you need to enable allow override:
sudo gedit /etc/apache2/apache2.conf

and enable .htaccess by changing

AllowOverride None
to
AllowOverride All

sudo systemctl restart apache2

- If after apache restart if you get errord in error.log: 
cat /var/log/apache2/error.log

Showing:
Invalid command 'RewriteEngine', perhaps misspelled or defined by a module not included in the server configuration

fix it by:

sudo a2enmod rewrite && sudo systemctl restart apache2

- When calling install.php if you get:

Fatal error: Uncaught Error: Call to undefined function simplexml_load_file() in ...

then you must install extensions in your php config:

sudo apt install php7.1-xml
sudo systemctl restart apache2

- If after calling install.php you get: 

Warning: fopen(/home/softcoder/www/svvfd/public_html/rr/application.log): failed to open stream: Permission denied in ...

then you must change access permission to application.log to:

-rw-rw-rw-  1 softcoder softcoder   120 Apr 13 10:15 application.log

- if after calling install.php you get:

Error detected, message : could not find driver, Code : 0

then you must check application.log if you see:

2017-04-13T10:15:38-07:00 ERROR DB Connect for: dsn [mysql:host=localhost;] user [myvfd] error [could not find driver] 

then you must:

sudo apt-get install php7.1-mysql 
sudo systemctl restart apache2

- If after calling install.php you get:

Error detected, message : SQLSTATE[HY000] [1045] Access denied for user 'myvfd'@'localhost' (using password: YES), Code : 1045

You need to make sure the mysql user specified exists and has access to connect to the server.

- If after calling install.php you get:

Fatal error: Uncaught PDOException: SQLSTATE[42000]: Syntax error or access violation: 1044 Access denied for user 'myvfd'@'%' to database 'myvfd' in ...

ensure mysql user has dba access.

- If after calling install.php you get:

Fatal error: Uncaught PDOException: SQLSTATE[HY000] [1049] Unknown database 'myvfd' in

create the database first, or goto install page: install.php

- Make sure your membership signup folder has grant execution access to scripts:

sudo chmod 777 -R ~/www/svvfd/public_html/

- If you get the error:

HTTP Error 404.3 - Not Found
The page you are requesting cannot be served because of the extension configuration. If the page is a script, add a handler. If the file should be downloaded, add a MIME map.

- Make sure you have installed php (7.1 x64) for iis using web platform installer

- If you get the error:

Call to undefined function finfo_open()

- You need to add the following line your php.ini then to activate it: (C:\Program Files\IIS Express\PHP\v7.1\php.ini)

extension=php_fileinfo.dll

- If you get the following error in the logs and no sms message is sent:

Curl error: SSL certificate problem: self signed certificate in certificate chain

- you must download: http://curl.haxx.se/ca/cacert.pem

edit php.ini

[curl]
curl.cainfo=c:/cert/cacert.pem

- If some of your users mobile devices do not show a clickable URL in the sms callout:

then your newer website domain name may not be recognized. For example some phones don't 
understand the following link because it uses a newer .solutions format:

https://vsoft.solutions/

- you must find a host that you have access to, which has a well known format example:

https://vejvoda.com/

create a folder on that host for example a folder named 'rr' and create a file name '.htaccess' 
in the 'rr' folder with the following content (notice rr matches the folder name you created, 
and the part to the right tells the webserver where to forward to, $1 copies url parameters):

RedirectMatch 301 /rr(.*) https://svvfd.vsoft.solutions$1

Next create a custom sms twig file in the root folder where config.php exists, inside a new folder 
you wil lcreated named:

views-custom

and name this file:

sms-callout-msg-custom.twig.html

with the following contents:

{% extends "sms-callout-msg.twig.html" %}

{% block sms_url_webroot %}
https://vejvoda.com/rr/
{% endblock %}

This will use the website: https://vejvoda.com/rr/ as a proxy to forward requests to: https://svvfd.vsoft.solutions
which all phones would recognize because it uses the well known .com format

Development:
--------------
Membership Signup uses composer for dependency management. Currently php 8.x is supported and our continuous integration system (Github actions) runs automated tests on those versions. If you want to contribute to membership signup as a developer checkout the repo from github and from the php folder of the repo on your local system run:

composer install

This will download all runtime and automated tests dependencies. If compser completed successfully you should be able to run the automated tests by running this command from the php folder:

phpunit

The Github actions CI automation results can be found here:

https://github.com/softcoder/membership-signup/actions/workflows/php.yml

Experimental Work:
------------------

Angular client:
---------------
We have begun porting the user interface to Angular (v8+). Currently this UI is partially ported from the
legacy twig UI, in order to build and deploy to your server:

- Install Node.js® and npm (https://nodejs.org/en/download/) if they are not already on your machine.
- Install the Angular CLI globally, open a console prompt: 

npm install -g @angular/cli

- Install project dependencies (the angular folder below is the folder you get from the git source tree):

cd angular
npm install

- Compile and Build the angular project:

ng build --base-href=/~softcoder/svvfd1/php/ngui/ --output-path=../php/ngui/ --aot

Notice above the base-href which is the document root path on your webserver where membership signup is installed (same folder where config.php exists). Also notice the compiled javascript project will be placed into the membership signup php/ngui folder.

- Copy the ngui folder to your web server's Root membership signup folder (same folder as config.php)
- visit the SPA (single page application) login page and try it out:
  
  /~softcoder/svvfd1/php/ngui/index.html

If you installed membership signup in the root folder of a subdomain for example http://svvfd.yourhost.com, you would run the script as follows:

ng build --base-href=/ngui/ --output-path=../php/ngui/ --aot

then copy the ngui folder to the root folder on svvfd.yourhost.com  

Serverless support:
-------------------
We have started work on the application architecture to prepare for supporting various vendors who offer serverless computing platforms. Documentation can be read regarding deploying to Google Cloud Run (GCR) using a docker container
https://github.com/softcoder/membership-signup/tree/master/docker  


Contributions:
--------------
Special thanks to all who have contributed to the success of this project. We accept patches and ideas from others and priortize based on time constraints and compatibility with our future direction.

Contributors currently include:
- The Caledonia Ramblers (https://caledoniaramblers.ca/) for all the great testing and feedback

Contact Info:
--------------
- Email: mark@vsoft.solutions

----

