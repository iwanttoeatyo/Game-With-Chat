<?php

namespace App\Controller;

use App\Model\Entity\Chat;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Cake\Datasource\ConnectionManager;

/**
 * WebSocket.php
 * WebSocket connection interface for WebSocket run by shell on port 2020
 *
 * Send any incoming messages to all clients grouped by a chat_id
 * Chat messages are saved into the database
 *
 * @property \App\Controller\Component\PlayerComponent $Player
 * @property \App\Controller\Component\ChatComponent $Chat
 *
 */
class WebSocket extends AppController implements MessageComponentInterface
{
	/**
	 * clients ConnectionInterface objects.
	 *
	 * @var \SplObjectStorage
	 */
	protected $clients;

	/**
	 * Contains reference to connectionInterface using connectionInterface's resourceId
	 * array[ConnectionInterface->resourceId] =  ConnectionInterface
	 *
	 * Example:
	 * `$users[$conn->resourceId] = $conn;`
	 *
	 * @var array[int]\Ratchet\ConnectionInterface
	 */
	private $users;

	/**
	 * Contains reference to User's user_id using connectionInterface's resourceId
	 * array[ConnectionInterface->resourceId] =  user_id
	 *
	 * Example:
	 * `$user_ids[$conn->resourceId] = $some_user_id;`
	 *
	 * @var array[int]int
	 */
	private $user_ids;

	/**
	 * Contains reference to User's subscribed chat_id using connectionInterface's resourceId
	 * array[ConnectionInterface->resourceId] = chat_id
	 *
	 * Example:
	 * `$subscriptions[$conn->resourceId] = $some_chat_id;`
	 *
	 * @var array[int]int
	 */
	private $subscriptions;

	/**
	 * Other components utilized by WebSocket
	 *
	 * @var array
	 */
	public $components = ['Player', 'Chat'];


	/**
	 * Initialization hook method.
	 * Loads Player and Chat components
	 * Initializes variables
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->loadComponent('Player');
		$this->loadComponent('Chat');
		$this->clients = new \SplObjectStorage;
		$this->subscriptions = [];
		$this->users = [];
		$this->user_ids = [];
	}

	/**
	 * When a client connects to the WebSocket server this function is run
	 * Attaches the clients connection interface into object storage
	 *
	 * @param ConnectionInterface $conn
	 */
	public function onOpen(ConnectionInterface $conn)
	{
		$this->clients->attach($conn);

		//Create reference to clients ConnectionInterface so they can be
		//sent messages at a later time.
		$this->users[$conn->resourceId] = $conn;

		//Reconnect to database if connection lost
		//This is required because connection is lost after a few hours
		$db_conn = ConnectionManager::get('default');
		if(!$db_conn->isConnected()){
			$db_conn->disconnect();
			$db_conn->connect();
		}

	}

	/**
	 * When a client sends a message to the WebSocket server it is received here
	 *
	 * Example msg for chat message
	 * `{"command":"message","msg":{"username":"a","message":"yes"}}`
	 *
	 *	Example msg for join chat
	 * `{"command":"joinChat","player_status":1,"chat_id":"1","user_id":"1"}`
	 *
	 * Examples of msg for a simple command
	 * `{"command":"closeLobby"}`
	 * `{"command":"updateLobby"}`
	 * `{"command":"updateLobbyList"}`
	 * `{"command":"startLobby"}`
	 *
	 * Example msg for gameOver
	 * `{"command":"gameOver","winner":1,"winner_name":"c"}`
	 *
	 * @param ConnectionInterface $conn
	 * @param string $msg The json message received from the client
	 */
	public function onMessage(ConnectionInterface $conn, $msg)
	{
		$targetChat = 0; //Default target chat to 0 which is not a valid chat

		$data = json_decode($msg);

		//logged to logs/cli-debug.log
		$this->log($data, 'info');
		$this->log($msg, 'info');

		//Get chat_id based on user connected if it exists.
		if (isset($this->subscriptions[$conn->resourceId]))
			$targetChat = $this->subscriptions[$conn->resourceId];

		switch ($data->command) {

			//Subscribe a user to the chat_id passed in by msg
			//Message all clients in global chat to updatePlayerList
			case "joinChat":
				$this->subscriptions[$conn->resourceId] = $data->chat_id;

				//Guests will not have a user_id
				//Actual users need to have their player status update to reflect where
				// they are in the application.
				/**  @see \App\Model\Entity\PlayerStatus */
				if ($data->user_id) {
					$this->user_ids[$conn->resourceId] = $data->user_id;
					$this->Player->setPlayerStatus($data->user_id, $data->player_status);
				}
				//Message all clients in global chat to update player list
				if ($data->user_id) {
					$this->emitCommandByChatId('updatePlayerList', Chat::GLOBAL_CHAT_ID);
				}

				break;
			case "message":
				if (isset($this->subscriptions[$conn->resourceId])) {
					if (empty($this->user_ids[$conn->resourceId]))
						$data->msg->username = "Guest " . $conn->resourceId;
					$saved = false;

					//Check if message is not unicode
					//Respond to user sending message
					if (strlen($msg) != strlen(utf8_decode($msg))) {
						$this->users[$conn->resourceId]->send(json_encode(array(
							'command' => 'message',
							'msg' => [
								'username' => '*System',
								'message' => 'Your message was not sent because it contained unsupported characters.'
							]
						)));
						break;
					}
					//Will throw exception on non standard text
					try {
						$saved = $this->Chat->createMessage($this->subscriptions[$conn->resourceId], $data->msg);
					} catch (\Exception $e) {
						$this->log($e);
					}

					//If message saved in db send it to all users in same chat
					if ($saved) {
						$this->emitMessageByChatId(json_encode($data), $targetChat);
					}
				}
				break;
			/** @noinspection PhpMissingBreakStatementInspection */
			case "closeLobby":
				//Send update lobby to everyone watching this lobby
				$this->emitCommandByChatId('closeLobby', $targetChat);
			/** @noinspection PhpMissingBreakStatementInspection */
			case "updateLobby":
				//Send update lobby to everyone watching this lobby
				$this->emitCommandByChatId('updateLobby', $targetChat);
			case "updateLobbyList":
				$this->emitCommandByChatId('updateLobbyList', Chat::GLOBAL_CHAT_ID);
				break;
			case "startLobby":
				//Send start lobby command to everyone watching this lobby that just started
				$this->emitCommandByChatId('startLobby', $targetChat);
				break;
			case "gameOver":
				//message contains command: 'gameOver', winner: 1 or 2, winner_name: 'asdsd'
				$this->emitMessageByChatId($msg, $targetChat);
				break;
			case "gameUpdate":
				$this->emitCommandByChatId('gameUpdate', $targetChat);
				break;
		}
	}

	/**
	 * When a client disconnect from the websocket server
	 *
	 * @param ConnectionInterface $conn
	 */
	public function onClose(ConnectionInterface $conn)
	{
		// The connection is closed, remove it, as we can no longer send it messages
		$this->clients->detach($conn);
		if (isset($this->user_ids[$conn->resourceId])) {
			//Set player to Offline
			$this->Player->setPlayerOffline($this->user_ids[$conn->resourceId]);
			//Make player leave Lobby
			//$this->Lobby->leave($this->user_ids[$conn->resourceId]);
		}


		unset($this->users[$conn->resourceId]);
		unset($this->subscriptions[$conn->resourceId]);
		unset($this->user_ids[$conn->resourceId]);
	}

	/**
	 * @param ConnectionInterface $conn
	 * @param \Exception $e
	 */
	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		echo "An error has occurred: {$e->getMessage()}\n";
		$conn->close();
	}


	/**
	 * Send single command to all clients on same chat_id
	 *
	 * @param string $command
	 * @param int $chat_id
	 */
	public function emitCommandByChatId($command, $chat_id)
	{
		foreach ($this->subscriptions as $socket_user_id => $user_chat_id) {
			if ($user_chat_id == $chat_id) {
				$this->users[$socket_user_id]->send(json_encode(array('command' => $command)));
			}
		}
	}

	/**
	 * Send full json msg to all clients on same chat_id
	 *
	 * @param string $msg
	 * @param int $chat_id
	 */
	public function emitMessageByChatId($msg, $chat_id)
	{
		foreach ($this->subscriptions as $socket_user_id => $user_chat_id) {
			if ($user_chat_id == $chat_id) {
				$this->users[$socket_user_id]->send($msg);
			}
		}
	}

}
