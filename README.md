# Game-With-Chat #
## You can find it at  https://urgame.me/  
### (Multiplayer online checkers using WebSockets and CakePHP 3)  
This project was created for CS372 at the University of Regina.  
This application uses a modified checkers game from https://github.com/codethejason/checkers  
Websocket is implemented using Ratchet PHP Websocket http://socketo.me/

## Current Server Specs (Hosted by Digital Ocean)
Ubuntu 16.04.1  
Nginx 1.10.0  
MySQL 5.7.17  
PHP 7.0.13  

## Getting it to run
Full CakePHP installation tutorial at https://book.cakephp.org/3.0/en/installation.html
### Install these first
Install PHP 7.0  
Install MySQL  
Setup a webserver like Apache or nginx  (Not required for local server. CakePHP has built in web server)
On Windows you can just install WAMP with PHP 7  

### Setup
Install Composer for php http://getcomposer.org/  
Setup a MySQL database called app
run the sql in [config/schema/urgame.sql](config/schema/urgame.sql) in the new app database

Checkout this repository and run composer in the main directory.  
Update composer dependencies. Composer should download all necessary dependencies
run `composer self-update`  
run `composer update`  

rename the file in [config/app.default.php ](config/app.default.php) to app.php
Modify the file to connect to your new database 'app' with the correct username and password

To Run the websocket server with apache or nginx you will need to do a https/ssl proxy through the web server.  
Example nginx file located in [config/nginx/game-with-chat](config/nginx/game-with-chat) This file goes in /etc/nginx/site-available/ 
Follow this guide for more information https://www.digitalocean.com/community/tutorials/how-to-install-nginx-on-ubuntu-16-04  

To Run the websocket server locally you will need to modify webroot/js/socket.js  
comment out `conn = new WebSocket('wss://' + document.domain + '/socket/');`   
uncomment `//conn = new WebSocket('ws://' + document.domain + ':2020');`  

Run Servers  
run `bin/cake server`  
run `bin/cake WebSocket`   

After running the server it should say the url the application is avaiable at.  
It should be something like localhost:8080/  

