# Game-With-Chat #
## Link to github https://github.com/mich4xD/Game-With-Chat
## DEMO NOT AVAILABLE
### (Multiplayer online checkers using WebSockets and CakePHP 3)  
This project was created for CS372 at the University of Regina.  

# Description
An online interface that allows players to play a checkers game against each other while any user (registered and unregistered) can watch any real-time game. The players will be competing to achieve a higher personal score.
Players are available to chat, spectate, create, and join lobbies.

# Implementation

## Programming Languages
Presentation layer
* HTML and CSS (Interface layout and style)
* JavaScript (Interface scripting, Ajax calls, WebSocket connection)
Business Layer
* PHP 7.0 (CakePHP web application)
Data Access Layer
 * SQL (MySQL queries)
## Re-used Frameworks, Libraries and Programs
Frameworks
* CakePHP 3 (https://cakephp.org) - an open-source web, rapid development framework that makes building web applications simpler, faster and require less code. It follows the model view controller (MVC) software architectural pattern.
* Bootstrap (http://getbootstrap.com) - a front-end framework that makes web development faster and easier.
Open source projects
* Checkers (https://github.com/codethejason/checkers) - an open-source checkers game written in HTML, CSS, and Javascript.
Libraries
* Ratchet (http://socketo.me/) - a PHP WebSocket library for serving real-time bidirectional messages between clients and server.
* jQuery (https://jquery.com/) - a small and fast JavaScript library for HTML document traversal and manipulation, event handling, and animation with Ajax support.
* jQuery UI (https://jqueryui.com) - a curated set of user interface interactions, effects, widgets, and themes built on top of the jQuery JavaScript Library.
Other
* Api-Gen (http://apigen.org) Documentation Generator

## Re-used code
**CakePHP 3** was used for the business layer and allowed us to easily set up our web applicationwith its built-in user authentication, form validation and database connection. The WebSocket server was integrated inside the CakePHP application and uses the components which have access to the database. This allowed the WebSocket server to be able to update the players and store messages in the database.  

**Bootstrap** was used to speed up the development of the layout by using the standard styling for
the navigation bar and other elements.  

**The open-source checkers project** used is a single player checkers game written in HTML and JavaScript. It was integrated into our application and converted into a multiplayer game. This allowed us to not have to worry about creating a checkers game from scratch and implementing all of the game logic.  

**The Ratchet PHP Websocket library** was used to setup a WebSocket server that allows the client to send messages directly to all other uses and to notify the other clients when parts of the application need to be updated. For example, when the game is started, the client who clicks the start button sends a message through the WebSocket notifying all other clients that the game has been started. These clients receive the WebSocket message and will use a JavaScript function to load the new page with the started checkers game.  

**jQuery and jQuery UI** were used to simplify and speed up the development of the JavaScript functions for the front-end logic of the application.  

**Api-Gen** was used to generate documentation from the source to help with writing the report and to help the other members understand the structure of the source code.

## Previous Server Specs (Hosted by Digital Ocean)
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
It should be something like built-in server is running in http://localhost:8765/

