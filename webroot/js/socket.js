$(function () {
	var COLORS = [
		'#e21400', '#91580f', '#f8a700', '#f78b00',
		'#58dc00', '#287b00', '#a8f07a', '#4ae8c4',
		'#3b88eb', '#3824aa', '#a700ff', '#d300e7'
	];

	var PLAYER_STATUS = {
		GLOBAL: 1,
		LOBBY: 2,
		GAME: 3
	};

	//HTTPS for Server
	var conn = new WebSocket('wss://' + document.domain + '/socket/');
	//Regular for local dev
	//var conn = new WebSocket('ws://' + document.domain + ':2020');


	var chat_id = $('#chat-id').val();
	var lobby_id = $('#lobby-id').val();
	var game_id = $('#game-id').val();
	var username = $('#username').val();
	var user_id = $('#user-id').val();
	var player1_id = $('#player1-id').val();
	var player2_id = $('#player2-id').val();
	var is_player1 = (user_id == player1_id);
	var is_player2 = (user_id == player2_id);
	var player_status;

	var $window = $(window);
	var $inputMessage = $('#input-message'); // Input message input box
	var $messages = $('.messages');
	var $lobbies = $('.lobbies');
	var $players = $('.players');
	var $player2_name = $('#player2-name');

	if (game_id)
		player_status = PLAYER_STATUS.GAME;
	else if (lobby_id)
		player_status = PLAYER_STATUS.LOBBY;
	else
		player_status = PLAYER_STATUS.GLOBAL

	makeSelectable();
	scrollDownChat();

//On websocket error
	conn.onerror = function (e) {
		addChatMessage({username: '*System', message: 'Can\'t connect to the server.'});
	};

//On Websocket first connection
	conn.onopen = function (e) {
		//Tell websocket to have this client join this chat channel
		conn.send(JSON.stringify({
			command: "joinChat",
			player_status: player_status,
			chat_id: chat_id,
			user_id: user_id
		}));
		//tell users to refresh lobbies if player 2 has joined.
		//dont update games that have started
		if (!game_id && (is_player2 || is_player1)) {
			sendUpdateLobby();
		}
		addChatMessage({username: '*System', message: 'You have connected to the server.'});
	};

	//On Websocket sends client a message
	conn.onmessage = function (e) {
		var data = JSON.parse(e.data);

		if (data)
			switch (data.command) {
				case "message":
					addChatMessage(data.msg);
					break;
				case "updateLobbyList":
					//Delay so we don't refresh lobby before database updates
					setTimeout(updateLobbyList, 200);
					break;
				case "updatePlayerList":
					updatePlayerList();
					break;
				case "updateLobby":
					//Delay so we don't refresh lobby before database updates
					setTimeout(updateLobby, 200);
					break;
				case "startLobby":
					setTimeout(redirectToGame, 2000);
					break;
				case "gameOver":
					displayWinner(data.winner, data.winner_name);
					break;
			}
	};

	function displayWinner(winner, winner_name) {
		var a = new CustomAlert();
		a.render(winner_name + "(Player " + winner + ") Wins.")
	}

	function sendUpdateLobby() {
		conn.send(JSON.stringify({
			command: "updateLobby",
			chat_id: chat_id
		}));
	}

	function sendStartLobby() {
		conn.send(JSON.stringify({
			command: "startLobby",
			chat_id: chat_id
		}));
	}

	function sendWinner(winner, winner_name) {
		conn.send(JSON.stringify({
			command: "gameOver",
			chat_id: chat_id,
			winner: winner,
			winner_name: winner_name
		}));
	}

	function updateLobby() {
		$.ajax({
			type: "POST",
			url: '/Lobbies/refreshLobby',
			data: {id: lobby_id},
			success: function (response) {
				refreshLobby(response);
			}
		});
	}

	//Reload page after game start. should put user in game
	function redirectToGame() {
		location.reload();
	}

	//Updates elements on page with data
	//expects data to have
	//player2_name and lobby_status
	function refreshLobby(data) {
		//fix player2 name
		if (is_player2 && data.player2_name) {
			($player2_name).text(data.player2_name);
			addChatMessage({username: '*System', message: data.player2_name + " has joined as Player 2."});
		} else {

			var string = $.trim($($player2_name).text());
			if (string != "Waiting for player to join...") {
				addChatMessage({username: '*System', message: "Player 2 has left."});
				$player2_name.text("Waiting for player to join...");
			}

		}
		if (data.lobby_status == "Full") {
			$('#start-lobby-btn').removeAttr("disabled");
		} else {
			$('#start-lobby-btn').attr("disabled", true);
		}
	}

	function updatePlayerList() {
		$.ajax({
			type: "POST",
			url: '/Home/getPlayerList',
			success: function (response) {
				$players.html(response)
			}
		});
	}

	function updateLobbyList() {
		$.ajax({
			type: "POST",
			url: '/Home/getLobbyList',
			success: function (response) {
				$lobbies.html(response)
			}
		});
	}

	// Sends a chat message
	function sendMessage() {
		var message = $inputMessage.val();
		// Prevent markup from being injected into the message
		message = cleanInput(message);
		// if there is a non-empty message and a socket connection
		if (message) {
			$inputMessage.val('');
			// tell server to execute 'new message' and send along one parameter
			conn.send(JSON.stringify({command: "message", msg: {username: username, message: message}}));
		}
	}

	function addChatMessage(data) {
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
	function addMessageElement(el) {
		var $el = $(el);
		$messages.append($el);
		scrollDownChat();

	}

	function scrollDownChat() {

		$messages.parent().closest('div')[0].scrollTop = $messages.parent().closest('div')[0].scrollHeight;
	}

	// Prevents input from having injected markup
	function cleanInput(input) {
		return $('<div/>').text(input).text();
	}

	// Gets the color of a username through our hash function
	function getUsernameColor(username) {
		// Compute hash code
		var hash = 7;
		for (var i = 0; i < username.length; i++) {
			hash = username.charCodeAt(i) + (hash << 5) - hash;
		}
		// Calculate color
		var index = Math.abs(hash % COLORS.length);
		return COLORS[index];
	}


	function makeSelectable() {
		$(".selectable").selectable({
			selected: function (event, ui) {
				//Unselect everything and reselect the one that was clicked
				$('.selectable .ui-selected').removeClass('ui-selected');
				$(ui.selected).addClass('ui-selected');

				var lobby_id = $(ui.selected).attr('lobby-id');
				var user_id = $(ui.selected).attr('user-id');

				//If selected lobby
				if (lobby_id) {
					getLobbyInfo(lobby_id);
				}
				//if selected player
				if (user_id) {
					getPlayerInfo(user_id);
				}
			}
		});
	}

	function getLobbyInfo(lobby_id) {
		$.ajax({
			type: "POST",
			url: '/Home/getLobbyInfo',
			data: {id: lobby_id},
			success: function (response) {
				$('.info-container').html(response);
			}
		});
	}


	function getPlayerInfo(user_id) {
		$.ajax({
			type: "POST",
			url: '/Home/getPlayerInfo',
			data: {id: user_id},
			success: function (response) {
				$('.info-container').html(response);
			}
		});
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

	$('#msg_btn').click(function () {
		sendMessage();
	});

	$('#leave-lobby-btn').click(function () {
		sendUpdateLobby();
	});

	$('#start-lobby-btn').click(function () {
		sendStartLobby();
	});

	$('#forfeit-game-btn').click(function (e) {
		e.preventDefault();
		if (window.confirm("Are you sure you want to forfeit?")) {
			sendForfeit();
		}
	});

	function sendForfeit() {
		console.log(game_id);
		$.ajax({
			type: "POST",
			url: '/Games/forfeit',
			data: {id: game_id},
			success: function (response) {
				console.log(response);
				if (response != null) {
					sendWinner(response.winner, response.winner_name);
				}else{
					console.log("no forfeit");
				}
			}
		});
	}
});