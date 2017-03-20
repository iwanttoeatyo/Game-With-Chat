// gloabal variables
var player2CapturedPiece = 0;
var player1CapturedPiece = 0;
window.onload = function() { 
    console.log("player1capturedpiece "+player1CapturedPiece);
  //The initial setup
  var gameBoard1 = [ 
    [  0,  1,  0,  1,  0,  1,  0,  1 ],
    [  1,  0,  1,  0,  1,  0,  1,  0 ],
    [  0,  1,  0,  1,  0,  1,  0,  1 ],
    [  0,  0,  0,  0,  0,  0,  0,  0 ],
    [  0,  2,  0,  2,  0,  2,  0,  2 ],
    [  2,  0,  2,  0,  2,  0,  2,  0 ],
    [  0,  2,  0,  2,  0,  2,  0,  2 ],
    [  2,  0,  2,  0,  2,  0,  2,  0 ]
  ];
    
  var jss=JSON.stringify(gameBoard1);
	
  var gameBoard= JSON.parse(jss);
  //arrays to store the instances
  var pieces = [];
  var tiles = []; 
  
  //distance formula
  var dist = function (x1, y1, x2, y2) {
    return Math.sqrt(Math.pow((x1-x2),2)+Math.pow((y1-y2),2));
  }
  //Piece object - there are 24 instances of them in a checkers game
  function Piece (element, position) {
    //linked DOM element
    this.element = element;
    //positions on gameBoard array in format row, column
    this.position = position; 
    //which player's piece i it
    this.player = '';

    //figure out player by piece id
    if(this.element.attr("id") < 12)
      this.player = 1;
    else
      this.player = 2;
    //makes object a king
    this.king = false;
    this.makeKing = function () {
      this.element.css("backgroundImage", "url('img/king"+this.player+".png')");
      this.king = true;
    }
    //moves the piece
    this.move = function (tile) { 
      this.element.removeClass('selected'); 
      if(!Board.isValidPlacetoMove(tile.position[0], tile.position[1])) return false;
      //make sure piece doesn't go backwards if it's not a king
      if(this.player == 1 && this.king == false) {
        if(tile.position[0] < this.position[0]) return false;
      } else if (this.player == 2 && this.king == false) {
        if(tile.position[0] > this.position[0]) return false;
      }
      //remove the mark from Board.board and put it in the new spot
      Board.board[this.position[0]][this.position[1]] = 0;
      Board.board[tile.position[0]][tile.position[1]] = this.player;
      this.position = [tile.position[0], tile.position[1]];
      //change the css using board's dictionary
      this.element.css('top', Board.dictionary[this.position[0]]);
      this.element.css('left', Board.dictionary[this.position[1]]);
      //if piece reaches the end of the row on opposite side crown it a king (can move all directions)
      if(!this.king && (this.position[0] == 0 || this.position[0] == 7 )) 
        this.makeKing();

      // check if someone wins
        if(player1CapturedPiece == 12){
            var Alert = new CustomAlert();
            Alert.render("Player1 win! \\(^o^)/");
        }
        else if(player2CapturedPiece == 12){
            var Alert = new CustomAlert();
            Alert.render("Player2 win! \\(^o^)/");
        } 
        
      Board.changePlayerTurn();
      return true;
    };
    
    //tests if piece can jump anywhere
    this.canJumpAny = function () {
      if(this.canOpponentJump([this.position[0]+2, this.position[1]+2]) ||
         this.canOpponentJump([this.position[0]+2, this.position[1]-2]) ||
         this.canOpponentJump([this.position[0]-2, this.position[1]+2]) ||
         this.canOpponentJump([this.position[0]-2, this.position[1]-2])) {
        return true;
      } return false;
    };
    
    //tests if an opponent jump can be made to a specific place
    this.canOpponentJump = function(newPosition) {
      //find what the displacement is
      var dx = newPosition[1] - this.position[1];
      var dy = newPosition[0] - this.position[0];
      //make sure object doesn't go backwards if not a king
      if(this.player == 1 && this.king == false) {
        if(newPosition[0] < this.position[0]) return false;
      } else if (this.player == 2 && this.king == false) {
        if(newPosition[0] > this.position[0]) return false;
      }
      //must be in bounds
      if(newPosition[0] > 7 || newPosition[1] > 7 || newPosition[0] < 0 || newPosition[1] < 0) return false;
      //middle tile where the piece to be conquered sits
      var tileToCheckx = this.position[1] + dx/2;
      var tileToChecky = this.position[0] + dy/2;
      //if there is a piece there and there is no piece in the space after that
      if(!Board.isValidPlacetoMove(tileToChecky, tileToCheckx) && Board.isValidPlacetoMove(newPosition[0], newPosition[1])) {
        //find which object instance is sitting there
        for(pieceIndex in pieces) {
          if(pieces[pieceIndex].position[0] == tileToChecky && pieces[pieceIndex].position[1] == tileToCheckx) {
            if(this.player != pieces[pieceIndex].player) {
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
      if(pieceToRemove) {
        pieces[pieceIndex].remove();
        return true;
      }
      return false;
    };
    
    this.remove = function () {
      //remove it and delete it from the gameboard
      this.element.css("display", "none");
      if(this.player == 1) {
          $('#player2').append("<div class='capturedPiece'></div>");
           player2CapturedPiece++;
          console.log("player2CapturedPiece: "+player2CapturedPiece);
      }
      if(this.player == 2) {
          $('#player1').append("<div class='capturedPiece'></div>");
           player1CapturedPiece++;
          console.log("player1CapturedPiece: "+player2CapturedPiece);
      }
      Board.board[this.position[0]][this.position[1]] = 0;
      //reset position so it doesn't get picked up by the for loop in the canOpponentJump method
      this.position = [];
    }
  }
  
  function Tile (element, position) {
    //linked DOM element
    this.element = element;
    //position in gameboard
    this.position = position;
    //if tile is in range from the piece
    this.inRange = function(piece) {
      if(dist(this.position[0], this.position[1], piece.position[0], piece.position[1]) == Math.sqrt(2)) {
        //regular move
        return 'regular';
      } else if(dist(this.position[0], this.position[1], piece.position[0], piece.position[1]) == 2*Math.sqrt(2)) {
        //jump move
        return 'jump';
      }
    };
  }
  
  //Board object - controls logistics of game
  var Board = {
    board: gameBoard,
    playerTurn: 1,
    tilesElement: $('div.tiles'),
    //dictionary to convert position in Board.board to the viewport units
    dictionary: ["0vmin", "8vmin", "16vmin", "24vmin", "32vmin", "40vmin", "48vmin", "56vmin", "64vmin", "72vmin"],
    //initialize the 8x8 board
    initalize: function () {
      var countPieces = 0;
      var countTiles = 0;
      for (row in this.board) { //row is the index
        for (column in this.board[row]) { //column is the index
          //whole set of if statements control where the tiles and pieces should be placed on the board
          if(row%2 == 1) {
            if(column%2 == 0) {
              this.tilesElement.append("<div class='tile' id='tile"+countTiles+"' style='top:"+this.dictionary[row]+";left:"+this.dictionary[column]+";'></div>");
              tiles[countTiles] = new Tile($("#tile"+countTiles), [parseInt(row), parseInt(column)]);
              countTiles += 1;
            }
          } else {
            if(column%2 == 1) {
              this.tilesElement.append("<div class='tile' id='tile"+countTiles+"' style='top:"+this.dictionary[row]+";left:"+this.dictionary[column]+";'></div>");
              tiles[countTiles] = new Tile($("#tile"+countTiles), [parseInt(row), parseInt(column)]);
              countTiles += 1;
            }
          }
          if(this.board[row][column] == 1) {
            $('.player1pieces').append("<div class='piece' id='"+countPieces+"' style='top:"+this.dictionary[row]+";left:"+this.dictionary[column]+";'></div>");
            pieces[countPieces] = new Piece($("#"+countPieces), [parseInt(row), parseInt(column)]);
            countPieces += 1;
          } else if(this.board[row][column] == 2) {
            $('.player2pieces').append("<div class='piece' id='"+countPieces+"' style='top:"+this.dictionary[row]+";left:"+this.dictionary[column]+";'></div>");
            pieces[countPieces] = new Piece($("#"+countPieces), [parseInt(row), parseInt(column)]);
            countPieces += 1;
          }
        }
      }
    },
    //check if the location has an object
    isValidPlacetoMove: function (row, column) {
      if(this.board[row][column] == 0) {
        return true;
      } return false;
    },
    //change the active player - also changes div.turn's CSS
    changePlayerTurn: function () {
        //console.log("player turn: "+this.playerTurn);
      if(this.playerTurn == 1) {
        this.playerTurn = 2;
          $("#player1Turn").css("background", "transparent");
          $("#player2Turn").css("background", "#BEEE62");
        return;
      }
      if(this.playerTurn == 2) {
        this.playerTurn = 1;
          $("#player1Turn").css("background", "#BEEE62");
          $("#player2Turn").css("background", "transparent");
      }
    },
      
    //reset the game
    clear: function () {
      location.reload(); 
    }
  }
  
  //initialize the board
  Board.initalize();
  
  /***
  Events
  ***/
  
  //select the piece on click if it is the player's turn
  $('.piece').on("click", function () {
    var selected;
    var isPlayersTurn = ($(this).parent().attr("class").split(' ')[0] == "player"+Board.playerTurn+"pieces");
    if(isPlayersTurn) {
      if($(this).hasClass('selected')) selected = true;
      $('.piece').each(function(index) {$('.piece').eq(index).removeClass('selected')});
      if(!selected) {
        $(this).addClass('selected');
      }
    }
  });
  
  //reset game when clear button is pressed
  $('#cleargame').on("click", function () {
    Board.clear();
  });
  
  //move piece when tile is clicked
  $('.tile').on("click", function () {
    //make sure a piece is selected
    if($('.selected').length != 0) {
      //find the tile object being clicked
      var tileID = $(this).attr("id").replace(/tile/, '');
      var tile = tiles[tileID];
      //find the piece being selected
      var piece = pieces[$('.selected').attr("id")];
      //check if the tile is in range from the object
      var inRange = tile.inRange(piece);
      if(inRange) {
        //if the move needed is jump, then move it but also check if another move can be made (double and triple jumps)
        if(inRange == 'jump') {
          if(piece.opponentJump(tile)) {
            piece.move(tile);
            if(piece.canJumpAny()) {
               Board.changePlayerTurn(); //change back to original since another turn can be made
               piece.element.addClass('selected');
            }
          } 
          //if it's regular then move it if no jumping is available
        } else if(inRange == 'regular') {
          if(!piece.canJumpAny()) {
            piece.move(tile);
          } else {
            alert("You must jump when possible!");
          }
        }
      }
    }
  });
  
}

// function to make a customized alert box    
function CustomAlert(){
    console.log("CustomAlert is called");
    this.render = function(dialog){
        var winW = window.innerWidth;
        var winH = window.innerHeight;
        var dialogoverlay = document.getElementById('dialogoverlay');
        var dialogbox = document.getElementById('dialogbox');
        dialogoverlay.style.display = "block";
        dialogoverlay.style.height = winH+"px";
        dialogbox.style.left = (winW/2) - (550 * .5)+"px";
        dialogbox.style.top = "100px";
        dialogbox.style.display = "block";
        document.getElementById('dialogboxhead').innerHTML = "Congratulations!";
        document.getElementById('dialogboxbody').innerHTML = "<h3>"+dialog+"</h3>";
        document.getElementById('dialogboxfoot').innerHTML = '<button onclick="Alert.ok()">OK</button>';
    }
	this.ok = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
    
} // end of CustomAlert()


// initialize your variables outside the function var 
count = 0; 
var clearTime; 
var seconds = 0, minutes = 5, hours = 0; 
var clearState; 
var secs, mins, gethours ; 
function startWatch( ) { 
    /* check if seconds is equal to 60 and add a +1 to minutes, and set seconds to 0 */ 
    if ( seconds === 0 ) { seconds = 60; minutes = minutes - 1; } 
    /* you use the javascript tenary operator to format how the minutes should look and add 0 to minutes if less than 10 */ 
    mins = ( minutes < 10 ) ? ( '0' + minutes + ': ' ) : ( minutes + ': ' ); 
    /* check if minutes is equal to 60 and add a +1 to hours set minutes to 0 */ 
    if ( minutes === 60 ) { minutes = 0; hours = hours + 1; } /* you use the javascript tenary operator to format how the hours should look and add 0 to hours if less than 10 */ gethours = ( hours < 10 ) ? ( '0' + hours + ': ' ) : ( hours + ': ' ); 
    secs = ( seconds < 10 ) ? ( '0' + seconds ) : ( seconds ); // display the stopwatch 
    var x = document .getElementById("timer"); 
    x.innerHTML = 'Time: ' + gethours + mins + secs; /* call the seconds counter after displaying the stop watch and increment seconds by +1 to keep it counting */ 
    seconds--; /* call the setTimeout( ) to keep the stop watch alive ! */ 
    clearTime = setTimeout( "startWatch( )", 1000 );
    // startWatch( ) //create a function to start the stop watch 
      if ( seconds === 0 && minutes === 0&& hours === 0 ) { /* hide the fulltime when the stop watch is running */ 
        //initiate function to check database for last move time and have the player forfiet 
      }

} 
 function startTime( ) { /* check if seconds, minutes, and hours are equal to zero and start the stop watch */ 
    
 
     var fulltime = document.getElementById( "fulltime" ); 
     fulltime.style.display = "none"; /* hide the start button if the stop watch is running */ 
     this.style.display = "none"; /* call the startWatch( ) function to execute the stop watch whenever the startTime( ) is triggered */ 
     startWatch( );  }// startwatch.js end // if () } // startTime() 
     /* you need to bind the startTime( ) function to any event type to keep the stop watch alive ! */ 


function restartTime( ) { /* check if seconds, minutes, and hours are equal to zero and start the stop watch */ 
    
 
     minutes = 5; 
     seconds=0;  }// startwatch.js end // if () } // startTime() 
     /* you need to bind the startTime( ) function to any event type to keep the stop watch alive ! */ 
     //window.addEventListener( 'load', function ( ) { var start = document .getElementById("start"); start.addEventListener( 'click', startTime ); });
