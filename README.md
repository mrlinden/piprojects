# piprojects

Just a simple project for my pi

1 Installed raspbian

2 Setup wlan, locale, keyboard etc.

3 install webserver
  sudo apt-get install lighttpd
  
4 install database
  sudo apt-get install mysql-server
  sudo apt-get install mysql-client phpmyadmin
  Set pwd to linden1mysql
  
5 install php (Perl is already installed as part of raspbian)
  sudo apt-get install php5-common php5-cgi
  sudo apt-get install php5
  sudo apt-get install php5-mysql
    Note it's important to install in the order listed above. 
    If you try to install php5 without first installing the 
    php5-cgi package then it will install Apache as well, 
    which we don't want for this light-weight lighttpd server.

6 To enable the server to handle php scripts the fastcgi-php module should be enabled by issuing in the command
  sudo lighty-enable-mod fastcgi-php
  Then reload the server using
  sudo service lighttpd force-reload
 
7 Set permissions on the web directory /var/www/
  It is useful to change the permissions on the www directory to allow your user to update the webpages without needing to be root.
  sudo chown www-data:www-data /var/www
  sudo chmod 775 /var/www
  sudo usermod -a -G www-data pi

  do the same for /var/www/html
  
8 Git checkout 
  in /home/pi
  checkout piprojects from https://github.com/mrlinden/piprojects.git
  
  create a symbolic link from /var/www/html/visit to /home/pi/piprojects/www/visit/
  
  

  


