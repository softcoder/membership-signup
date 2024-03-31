[![Build Status](https://github.com/softcoder/membership-signup/actions/workflows/php.yml/badge.svg)](https://github.com/softcoder/membership-signup/actions/workflows/php.yml)

To see detailed unit test and code coverage stats visit: https://github.com/softcoder/membership-signup/actions/workflows/php.yml

Membership-Signup
=================

<img src="https://github.com/softcoder/membership-signup/blob/main/screenshots/iconXX.png" align="left" >

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

This PHP app requires NO database and requires one library to process the PDF transformation of data from the webpage to the PDF document, the PDF library used is called PDFtk. Most of the behaviour is configurable.

Technology:
-------------------------
Membership Signup was written using PHP for its backend server operations and html5 + javascript for the frontend.

System Requirements:
--------------------
- An email account that can send emails to members
- The PDFtk tool (free for non-commercial use) https://www.pdflabs.com/tools/pdftk-server/
- A webserver that can run PHP 8.x (such as Apache, IIS or NGinx)
- PDF documents with named fields acting as templates

Docker Image:
-------------
We provide a docker image with the basic environment installed here:
https://hub.docker.com/repository/docker/softcoder/membership-signup/

Example deployment to digitalocean.com (xxxxx is your API key):

	docker-machine create --driver digitalocean --digitalocean-access-token xxxxx membership-signup
	docker-machine env membership-signup
	eval $(docker-machine env membership-signup)
	docker run -d -p "80:80" --name membership-signup-demo softcoder/membership-signup:latest

Discover the ip address of the droplet for example:

        docker-machine ip membership-signup
        138.197.70.45

Goto the web url where you installed the docker image and test:

        http://138.197.70.45/

In order to set config values, you can override them as env variables:

        docker run -p "80:80" -v ${PWD}/app:/app -e APP_SMTP_OutboundUsername='myemail@gmail.com' -e APP_SMTP_OutboundPassword='xx123' -e APP_SMTP_OutboundFromAddress='myemail@gmail.com' softcoder/membership-signup:latest

This assumes that the host (outside of the docker container) has a folder named:

        app/    <-- containing all the php files for membership-signup

There is also a Dockerfile for deployment to Google Cloud Run full details:
https://github.com/softcoder/membership-signup/tree/main/docker

Installation:
-------------
Getting started video - basic installation on Linux (click the image below):

[![Getting Started install](http://img.youtube.com/vi/HfDnYGbhWRQ/0.jpg)](https://youtu.be/HfDnYGbhWRQ)

General installation steps:
---------------------------
- Download the application either using git commands (for those who know how to use git) or download the master archive here: https://github.com/softcoder/membership-signup/archive/master.zip and extract to a folder on your local PC.
- Edit the values in [config-default.php](config-default.php) to suit your environment. (see Configuration section below)
- Rename the file config-default.php to config.php
- Upload the files in the root folder to a location on your webserver (this will be the root folder for membership-signup).
- If using IIS (Apache users skip to 'Open the url') you should import the file [IIS_Import.htaccess](IIS_Import.htaccess) following these steps:
-  1. Start IIS Manager. 
-  2. On the left, in the Connections pane, select 'Sites' then 'Default Web Site'.
-  3. Create a new virtual folder pointing to the root php folder of membership signup (example alias svvfd)
-  4. With the alias selected (example svvfd) click on the right, in Features View, IIS, click URL Rewrite.
-  5. On the right, in the Actions pane, click 'Open Feature'.
-  6. On the right, in the Actions pane, click 'Import Rules'.
-  7. Select the file IIS_import.htaccess using the ... elipses and import, then click apply.
- On the server run the following command from the membership signup root folder: composer install
- Open the url: http://www.yourwebserver.com/uploadlocation/
- If everything was done correctly you should see a member form index page.

A Linux based bash script that handles most of the installation steps may look like:

#!/bin/bash
echo 'Downloading Membership Signup files...'
git clone https://github.com/softcoder/membership-signup.git

echo 'Installing dependencies using Composer...'
cd membership-signup
composer install

echo 'Moving config-default.php to config.php...'
mv config-default.php config.php

echo 'Please edit the values in config.php and then visit the URL root where you installed this application...'

Manually installing PDFtk on a Linux distribution:
--------------------------------------------------
It may be possible to manually install the PDFtk library on your Linux distribution by following these steps:

#!/bin/sh  
script_dir=$(dirname $0)  
export LD_LIBRARY_PATH=${script_dir}/  
echo $script_dir  
#download the correct binary for your distribution  
exec wget https://www.linuxglobal.com/static/blog/pdftk-2.02-1.el7.x86_64.rpm  
#extract the PDFtk package  
exec rpm2cpio pdftk-2.02-1.el7.x86_64.rpm | cpio -idmv  
cp ${script_dir}/usr/bin/pdftk ${script_dir}/usr/bin/pdftk-bin  
cp ${script_dir}/pdftk.sh ${script_dir}/usr/bin/pdftk  
cp ${script_dir}/usr/lib64/libgcj.so.10.0.0 ${script_dir}/usr/bin/libgcj.so.10  

Configuration:
--------------

The most important information that you require to configure is located in config.php. 
You must create this file (or rename [config-default.php](config-default.php) to config.php) and supply configuration values.
The following explains the main sections in config.php. The structures used in coinfig.php are
defined in [config_interfaces.php](config_interfaces.php) if you are interested to see their definitions.


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

- you must download: http://curl.haxx.se/ca/cacert.pem

edit php.ini

[curl]
curl.cainfo=c:/cert/cacert.pem


Development:
--------------
Membership Signup uses composer for dependency management. Currently php 8.x is supported and our continuous integration system (Github actions) runs automated tests on those versions. If you want to contribute to membership signup as a developer checkout the repo from github and from the php folder of the repo on your local system run:

composer install

This will download all runtime and automated tests dependencies. If compser completed successfully you should be able to run the automated tests by running this command from the php folder:

phpunit

The Github actions CI automation results can be found here:

https://github.com/softcoder/membership-signup/actions/workflows/php.yml

Contributions:
--------------
Special thanks to all who have contributed to the success of this project. We accept patches and ideas from others and prioritize based on time constraints and compatibility with our future direction.

Contributors currently include:
- The Caledonia Ramblers (https://caledoniaramblers.ca/) for all the great testing and feedback

Contact Info:
--------------
- Email: mark@vsoft.solutions

----

