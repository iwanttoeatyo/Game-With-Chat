$(function () {
	var COLORS = [
		'#e21400', '#91580f', '#f8a700', '#f78b00',
		'#58dc00', '#287b00', '#a8f07a', '#4ae8c4',
		'#3b88eb', '#3824aa', '#a700ff', '#d300e7'
	];

	var PLAYER_STATUS = {
		GLOBAL : 1,
		LOBBY:  2,
		GAME : 3
	};

	var conn = new WebSocket('wss://' + document.domain + '/socket/');
	//var conn = new WebSocket('ws://' + document.domain + ':2020');

	var chat_id = $('#chat-id').val();
	var lobby_id = $('#lobby-id').val();
	var game_id = $('#game-id').val();
	var username = $('#username').val();
	var user_id = $('#user-id').val();
	var $window = $(window);
	var $inputMessage = $('#input-message'); // Input message input box
	var $messages = $('.messages');
	var $lobbies = $('.lobbies');
	var $players = $('.players');

	var player_status = 0;

	if(game_id)
		player_status = PLAYER_STATUS.GAME;
	else if(lobby_id)
		player_status = PLAYER_STATUS.LOBBY;
	else
		player_status = PLAYER_STATUS.GLOBAL


	$(document).ready(function(){
		scrollDownChat();
	});

	conn.onopen = function (e) {
		console.log("Connection established!");
		console.log(JSON.stringify({command: "joinChat", player_status: player_status, chat_id: chat_id, user_id: user_id}));
			conn.send(JSON.stringify({command: "joinChat", player_status: player_status, chat_id: chat_id, user_id: user_id}));

	};

	conn.onmessage = function (e) {
		var data = JSON.parse(e.data);

		if(data)
			console.log(data);
			switch(data.command){
				case "message":
					addChatMessage(data.msg);
					break;
				case "updatePlayers":
					updatePlayers();
			}


	};

	function updatePlayers(){
		$.ajax({
			type: "POST",
			url: '/home/getPlayerList',
			success: function(response){
				console.log(response);
				$players.empty();
				for(var i = 0; i < response.length; i++){
					$players.append(addPlayer(response[i]));
				}

			}
		});
	}

	function addPlayer(player){
		var $usernameDiv = $('<span/>')
				.text(player.username)
		var $badgeDiv = $('<span class="badge"/>')
		console.log(player);
		console.log(player.player_status_id);
		switch(player.player_status_id){
			case PLAYER_STATUS.GLOBAL:
				$badgeDiv.addClass("btn-success");
				break;
			case PLAYER_STATUS.LOBBY:
				$badgeDiv.addClass("btn-primary");
				break;
			case PLAYER_STATUS.GAME:
				$badgeDiv.addClass("btn-danger")
		}
		$badgeDiv.text("In " + player.player_status.player_status);

		var $playerDiv = $('<li user-id="'+ player.id +'" class="list-group-item">')
				.append($usernameDiv,$badgeDiv);

		return $playerDiv;
	}

	// Sends a chat message
	function sendMessage() {
		var message = $inputMessage.val();
		if(username == null){
			username = "Guest";
		}
		// Prevent markup from being injected into the message
		message = cleanInput(message);
		// if there is a non-empty message and a socket connection
		if (message) {
			$inputMessage.val('');
			// tell server to execute 'new message' and send along one parameter
			conn.send(JSON.stringify({command: "message", msg: {username:username, message: message}}));
		}
	}

	function addChatMessage(data){
		var $usernameDiv = $('<span class="username"/>')
				.text(data.username + ": ")
				.css('color', getUsernameColor(data.username));
		var $messageBodyDiv = $('<span class="message-body">')
				.text(data.message);

		var $messageDiv = $('<li class="list-group-item message"/>')
				.data('username', data.username)
				.append($usernameDiv, $messageBodyDiv);

		addMessageElement($messageDiv);
	}

	// Adds a message element to the messages and scrolls to the bottom
	// el - The element to add as a message
	function addMessageElement (el) {
		var $el = $(el);
		$messages.append($el);
		scrollDownChat();

	}

	function scrollDownChat(){

		$messages.parent().closest('div')[0].scrollTop = $messages.parent().closest('div')[0].scrollHeight;
	}

	// Prevents input from having injected markup
	function cleanInput (input) {
		return $('<div/>').text(input).text();
	}

	// Gets the color of a username through our hash function
	function getUsernameColor (username) {
		// Compute hash code
		var hash = 7;
		for (var i = 0; i < username.length; i++) {
			hash = username.charCodeAt(i) + (hash << 5) - hash;
		}
		// Calculate color
		var index = Math.abs(hash % COLORS.length);
		return COLORS[index];
	}

	$window.keydown(function (event) {
		// Auto-focus the current input when a key is typed
		if (!(event.ctrlKey || event.metaKey || event.altKey)) {
			$inputMessage.focus();
		}

		// When the client hits ENTER on their keyboard
		if (event.which === 13) {
				sendMessage();
			}
	});

});