CREATE TABLE Player_Statuses
(
	id INT NOT NULL PRIMARY KEY,
    player_status VARCHAR(32)
);

INSERT INTO Player_Statuses (id, player_status) VALUES (0, 'Offline');
INSERT INTO Player_Statuses (id, player_status) VALUES (1, 'Global');
INSERT INTO Player_Statuses (id, player_status) VALUES (2, 'Lobby');
INSERT INTO Player_Statuses (id, player_status) VALUES (3, 'Game');

CREATE TABLE Users (
  id  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  email VARCHAR (255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_date DATETIME NOT NULL,
  player_status_id INT NOT NULL,
  FOREIGN KEY (player_status_id) REFERENCES Player_Statuses(id)
);


CREATE TABLE Scores (
  id INT NOT NULL PRIMARY KEY,
  user_id INT NOT NULL,
  win_count INT,
  loss_count INT,
  draw_count INT,
  FOREIGN KEY (user_id) REFERENCES Users(id)
);

CREATE TABLE Game_Statuses (
  id INT NOT NULL PRIMARY KEY,
  game_status VARCHAR (32)
);

INSERT INTO Game_Statuses (id, game_status) VALUES (0, 'Active');
INSERT INTO Game_Statuses (id, game_status) VALUES (1, 'Ended');

CREATE TABLE Lobby_Statuses (
  id INT NOT NULL PRIMARY KEY,
  lobby_status VARCHAR (32)
);

INSERT INTO Lobby_Statuses (id, lobby_status) VALUES (0, 'Open');
INSERT INTO Lobby_Statuses (id, lobby_status) VALUES (1, 'Full');
INSERT INTO Lobby_Statuses (id, lobby_status) VALUES (2, 'Started');
INSERT INTO Lobby_Statuses (id, lobby_status) VALUES (3, 'Closed');

CREATE TABLE Chats (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE Lobbies (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR (512) NOT NULL,
  lobby_status_id INT NOT NULL,
  player1_user_id INT NOT NULL,
  player2_user_id INT NULL,
  chat_id INT NOT NULL,
  created_date DATETIME NOT NULL,
  is_locked BOOLEAN NOT NULL,
  FOREIGN KEY (player1_user_id) REFERENCES Users(id),
  FOREIGN KEY (player2_user_id) REFERENCES Users(id),
  FOREIGN KEY (chat_id) REFERENCES Chats(id),
  FOREIGN KEY (lobby_status_id) REFERENCES Lobby_Statuses(id),
  PRIMARY KEY (id)
);

CREATE TABLE Games (
  id INT NOT NULL AUTO_INCREMENT,
  lobby_id INT NOT NULL,
  game_status_id INT NOT NULL,
  game_state JSON NOT NULL,
  player_turn INT NOT NULL,
  FOREIGN KEY (game_status_id) REFERENCES Game_Statuses (id),
  FOREIGN KEY (lobby_id) REFERENCES Lobbies(id),
  PRIMARY KEY (id)
);

CREATE TABLE Messages (
  id INT NOT NULL AUTO_INCREMENT,
  chat_id INT NOT NULL,
  created_date DATETIME NOT NULL,
  message TEXT(65534) NULL,
  username VARCHAR(255) NOT NULL,
  FOREIGN KEY (chat_id) REFERENCES Chats(id),
  FOREIGN KEY (username) REFERENCES Users(username),
  PRIMARY KEY (id)
);