// gloabal variables
var capturedPieces = new Array(3).fill(0);
var Board;
var pieces = [];
$(function () {
	//The initial setup
	var gameBoard = [
		[0, 1, 0, 1, 0, 1, 0, 1],
		[1, 0, 1, 0, 1, 0, 1, 0],
		[0, 1, 0, 1, 0, 1, 0, 1],
		[0, 0, 0, 0, 0, 0, 0, 0],
		[0, 0, 0, 0, 0, 0, 0, 0],
		[2, 0, 2, 0, 2, 0, 2, 0],
		[0, 2, 0, 2, 0, 2, 0, 2],
		[2, 0, 2, 0, 2, 0, 2, 0]
	];
	//arrays to store the instances

	var tiles = [];

	//distance formula
	var dist = function (x1, y1, x2, y2) {
		return Math.sqrt(Math.pow((x1 - x2), 2) + Math.pow((y1 - y2), 2));
	};
	//Piece object - there are 24 instances of them in a checkers game
	function Piece(element, position, player_id) {
		//linked DOM element
		this.element = element;
		//positions on gameBoard array in format row, column
		this.position = position;
		//which player's piece i it
		this.player = player_id;
		this.id = element.attr("id");

		//makes object a king
		this.king = false;
		this.makeKing = function () {
			this.element.addClass("king"+this.player);
			this.king = true;
		}
		//moves the piece
		this.move = function (tile) {

			this.element.removeClass('selected');
			if (!Board.isValidPlacetoMove(tile.position[0], tile.position[1])) {
				return false;
			}

			//make sure piece doesn't go backwards if it's not a king
			if (this.player == 1 && this.king == false) {
				if (tile.position[0] < this.position[0]) {
					return false;
				}
			} else if (this.player == 2 && this.king == false) {
				if (tile.position[0] > this.position[0]) {
					return false;
				}
			}
			//remove the mark from Board.board and put it in the new spot
			Board.board[this.position[0]][this.position[1]] = 0;
			Board.board[tile.position[0]][tile.position[1]] = this.player;
			this.position = [tile.position[0], tile.position[1]];
			//change the css using board's dictionary
			this.element.css('top', Board.dictionary[this.position[0]]);
			this.element.css('left', Board.dictionary[this.position[1]]);
			//if piece reaches the end of the row on opposite side crown it a king (can move all directions)
			if (!this.king && (this.position[0] == 0 || this.position[0] == 7 ))
				this.makeKing();

			Board.changePlayerTurn();
			return true;
		};

		//tests if piece can jump anywhere
		this.canJumpAny = function () {
			if (this.canOpponentJump([this.position[0] + 2, this.position[1] + 2]) ||
					this.canOpponentJump([this.position[0] + 2, this.position[1] - 2]) ||
					this.canOpponentJump([this.position[0] - 2, this.position[1] + 2]) ||
					this.canOpponentJump([this.position[0] - 2, this.position[1] - 2])) {
				return true;
			}
			return false;
		};

		//tests if an opponent jump can be made to a specific place
		this.canOpponentJump = function (newPosition) {
			//find what the displacement is
			var dx = newPosition[1] - this.position[1];
			var dy = newPosition[0] - this.position[0];
			//make sure object doesn't go backwards if not a king
			if (this.player == 1 && this.king == false) {
				if (newPosition[0] < this.position[0]) return false;
			} else if (this.player == 2 && this.king == false) {
				if (newPosition[0] > this.position[0]) return false;
			}
			//must be in bounds
			if (newPosition[0] > 7 || newPosition[1] > 7 || newPosition[0] < 0 || newPosition[1] < 0) return false;
			//middle tile where the piece to be conquered sits
			var tileToCheckx = this.position[1] + dx / 2;
			var tileToChecky = this.position[0] + dy / 2;
			//if there is a piece there and there is no piece in the space after that
			if (!Board.isValidPlacetoMove(tileToChecky, tileToCheckx) && Board.isValidPlacetoMove(newPosition[0], newPosition[1])) {
				//find which object instance is sitting there
				for (pieceIndex in pieces) {
					if (pieces[pieceIndex].position[0] == tileToChecky && pieces[pieceIndex].position[1] == tileToCheckx) {
						if (this.player != pieces[pieceIndex].player) {
							//return the piece sitting there
							return pieces[pieceIndex];
						}
					}
				}
			}
			return false;
		};

		this.opponentJump = function (tile) {
			var pieceToRemove = this.canOpponentJump(tile.position);
			//if there is a piece to be removed, remove it
			if (pieceToRemove) {
				pieces[pieceIndex].remove();
				return true;
			}
			return false;
		};

		this.remove = function () {
			//remove it and delete it from the gameboard
			this.element.css("display", "none");
			if (this.player == 1) {
				$('#player2').append("<div class='capturedPiece'></div>");
				capturedPieces[2]++
			}

			if (this.player == 2) {
				$('#player1').append("<div class='capturedPiece'></div>");
				capturedPieces[1]++;
			}
			Board.board[this.position[0]][this.position[1]] = 0;
			//reset position so it doesn't get picked up by the for loop in the canOpponentJump method
			this.position = [];
			this.player = 0;
		}
	}

	function Tile(element, position) {
		//linked DOM element
		this.element = element;
		//position in gameboard
		this.position = position;
		//if tile is in range from the piece
		this.inRange = function (piece) {
			if (dist(this.position[0], this.position[1], piece.position[0], piece.position[1]) == Math.sqrt(2)) {
				//regular move
				return 'regular';
			} else if (dist(this.position[0], this.position[1], piece.position[0], piece.position[1]) == 2 * Math.sqrt(2)) {
				//jump move
				return 'jump';
			}
		};
	}

//Board object - controls logistics of game
	Board = {
		board: gameBoard,
		playerTurn: 1,
		tilesElement: $('div.tiles'),
		//dictionary to convert position in Board.board to the viewport units
		dictionary: ["0vmin", "8vmin", "16vmin", "24vmin", "32vmin", "40vmin", "48vmin", "56vmin", "64vmin", "72vmin"],
		//initialize the 8x8 board
		initialize: function () {
			var countPieces = 0;
			var countTiles = 0;
			pieces = [];
			for (row in this.board) { //row is the index
				for (column in this.board[row]) { //column is the index
					//whole set of if statements control where the tiles and pieces should be placed on the board
					if (row % 2 == 1) {
						if (column % 2 == 0) {
							this.tilesElement.append("<div class='tile' id='tile" + countTiles + "' style='top:" + this.dictionary[row] + ";left:" + this.dictionary[column] + ";'></div>");
							tiles[countTiles] = new Tile($("#tile" + countTiles), [parseInt(row), parseInt(column)]);
							countTiles += 1;
						}
					} else {
						if (column % 2 == 1) {
							this.tilesElement.append("<div class='tile' id='tile" + countTiles + "' style='top:" + this.dictionary[row] + ";left:" + this.dictionary[column] + ";'></div>");
							tiles[countTiles] = new Tile($("#tile" + countTiles), [parseInt(row), parseInt(column)]);
							countTiles += 1;
						}
					}
					if (this.board[row][column] == 1) {
						$('.player1pieces').append("<div class='piece' id='" + countPieces + "' style='top:" + this.dictionary[row] + ";left:" + this.dictionary[column] + ";'></div>");
						pieces[countPieces] = new Piece($("#" + countPieces), [parseInt(row), parseInt(column)], 1);
						countPieces += 1;
					} else if (this.board[row][column] == 2) {
						$('.player2pieces').append("<div class='piece' id='" + countPieces + "' style='top:" + this.dictionary[row] + ";left:" + this.dictionary[column] + ";'></div>");
						pieces[countPieces] = new Piece($("#" + countPieces), [parseInt(row), parseInt(column)], 2);
						countPieces += 1;
					}
				}
			}
		},
		setPiecesInBoard: function (_pieces) {
			$('.player1pieces').html("");
			$('.player2pieces').html("");
			for (var i = 0; i < pieces.length; i++) {
				pieces[i].position = _pieces[i].position;
				pieces[i].king = _pieces[i].king;
				var kingable = "";
				if(pieces[i].king == true){
					kingable = " king" + pieces[i].player;
				}
				var id = pieces[i].id;
				var row = pieces[i].position[0];
				var column = pieces[i].position[1];
				if (row == null && column == null) {
					continue;
				}
				if (pieces[i].player == 1) {
					$('.player1pieces').append("<div class='piece"+ kingable +"' id='" + id + "' style='top:" +
							this.dictionary[row] + ";left:" + this.dictionary[column] + ";'></div>");
				} else if (pieces[i].player == 2) {
					$('.player2pieces').append("<div class='piece"+ kingable +"' id='" + id + "' style='top:" +
							this.dictionary[row] + ";left:" + this.dictionary[column] + ";'></div>");
				}
				pieces[i].element = $('#' + id);

			}

		}
		,
		//check if the location has an object
		isValidPlacetoMove: function (row, column) {
			return (this.board[row][column] == 0)
		},
		//change the active player - also changes div.turn's CSS
		changePlayerTurn: function () {
			if (this.playerTurn == 1) {
				this.playerTurn = 2;
				$("#player1Turn").css("background", "transparent");
				$("#player2Turn").css("background", "#BEEE62");
				return;
			}
			if (this.playerTurn == 2) {
				this.playerTurn = 1;
				$("#player1Turn").css("background", "#BEEE62");
				$("#player2Turn").css("background", "transparent");
			}
		},
		updatePlayerTurnDisplay: function (player) {
			if (player == 2) {
				$("#player1Turn").css("background", "transparent");
				$("#player2Turn").css("background", "#BEEE62");
			}
			if (player == 1) {
				$("#player1Turn").css("background", "#BEEE62");
				$("#player2Turn").css("background", "transparent");
			}
		}

	};

//initialize the board
	Board.initialize();
//Check if there is a board in the server
	getGameState();
	clickablePieces();
	/***
	 Events
	 ***/

//select the piece on click if it is the player's turn


//reset game when clear button is pressed
	$('#cleargame').on("click", function () {
		Board.clear();
	});

//move piece when tile is clicked
	$('.tile').on("click", function () {
		//make sure a piece is selected
		if ($('.selected').length != 0) {
			//find the tile object being clicked
			var tileID = $(this).attr("id").replace(/tile/, '');
			var tile = tiles[tileID];
			//find the piece being selected
			var id = ($('.selected').attr("id"));
			var piece = pieces[id];
			//check if the tile is in range from the object
			var inRange = tile.inRange(piece);
			if (inRange) {
				//if the move needed is jump, then move it but also check if another move can be made (double and triple jumps)
				if (inRange == 'jump') {
					if (piece.opponentJump(tile)) {
						piece.move(tile);
						if (piece.canJumpAny()) {
							Board.changePlayerTurn(); //change back to original since another turn can be made
							piece.element.addClass('selected');
						}
						//Move made Send Board
						updateGameState(Board, pieces, capturedPieces);
					}
					//if it's regular then move it if no jumping is available
				} else if (inRange == 'regular') {
					if (!piece.canJumpAny()) {
						piece.move(tile);
						//Move made Send Board
						updateGameState(Board, pieces, capturedPieces);
					} else {
						alert("You must jump when possible!");
					}
				}
			}
		}
	});

})
;

// function to make a customized alert box    
function CustomAlert() {
	this.render = function (title, dialog) {
		var winW = window.innerWidth;
		var winH = window.innerHeight;
		var dialogoverlay = document.getElementById('dialogoverlay');
		var dialogbox = document.getElementById('dialogbox');
		dialogoverlay.style.display = "block";
		dialogoverlay.style.height = winH + "px";
		dialogbox.style.left = (winW / 2) - (550 * .5) + "px";
		dialogbox.style.top = "100px";
		dialogbox.style.display = "block";
		document.getElementById('dialogboxhead').innerHTML = title;
		document.getElementById('dialogboxbody').innerHTML = "<h3>" + dialog + "</h3>";
		document.getElementById('dialogboxfoot').innerHTML = '<button onclick="new CustomAlert().ok()">OK</button>';

	};
	this.ok = function () {
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
		reloadLobby();
	}
} // end of CustomAlert()

function setBoard(_board, _pieces, _captured) {
	Board.board = _board.board;
	Board.playerTurn = _board.playerTurn;
	Board.setPiecesInBoard(_pieces);
	Board.updatePlayerTurnDisplay(_board.playerTurn);
	setCapturedPieces(_captured);
	clickablePieces();
}

function clickablePieces (){
	$('.piece').unbind("click");
	$('.piece').on("click", function () {
		var selected;
		var isPlayersTurn = ($(this).parent().attr("class").split(' ')[0] == "player" + Board.playerTurn + "pieces");
		if (isPlayersTurn && player_num == Board.playerTurn) {
			if ($(this).hasClass('selected')) selected = true;
			$('.piece').removeClass('selected');
			if (!selected) {
				$(this).addClass('selected');
			}
		}
	});
}

function setCapturedPieces(_capturedPieces) {
	capturedPieces[1] = _capturedPieces[1];
	capturedPieces[2] = _capturedPieces[2];
	$('.capturedPiece').remove();
	for (var i = 0; i < capturedPieces[1]; i++) {
		$('#player1').append("<div class='capturedPiece'></div>");
	}
	for (var i = 0; i < capturedPieces[2]; i++) {
		$('#player2').append("<div class='capturedPiece'></div>");
	}
}