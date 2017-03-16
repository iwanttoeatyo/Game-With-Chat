<?php

namespace App\Controller;

use App\Model\Entity\Chat;
use App\Model\Entity\PlayerStatus;
use Cake\Core\Exception\Exception;
use Cake\ORM\TableRegistry;
use DateTime;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Controller\Component\PlayerComponent;

/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
 *
 * @property \App\Controller\Component\PlayerComponent $Player
 * @property \App\Controller\Component\LobbyComponent $Lobby
 * @property \App\Controller\Component\ChatComponent $Chat
 *
 */
class WebSocketController extends AppController implements MessageComponentInterface
{
	protected $clients;
	private $users;
	private $user_ids;
	private $subscriptions;
	private $Messages;


	public function initialize()
	{
		$this->loadComponent('Player');
		$this->loadComponent('Lobby');
		$this->loadComponent('Chat');
		$this->Messages = TableRegistry::get('Messages');
		$this->clients = new \SplObjectStorage;
		$this->subscriptions = [];
		$this->users = [];
		$this->user_ids = [];
	}

	public function onOpen(ConnectionInterface $conn)
	{
		$this->clients->attach($conn);
		//Store
		$this->users[$conn->resourceId] = $conn;

	}

	public function onMessage(ConnectionInterface $conn, $msg)
	{
		$targetChat = 0; //If not able to get target chat then send to no chat
		$data = json_decode($msg);
		echo(dump($data));

		//Get chat_id based on user connected if it exists.
		if(isset( $this->subscriptions[$conn->resourceId]))
			$targetChat = $this->subscriptions[$conn->resourceId];

		switch ($data->command) {
			case "joinChat":
				//key = users connection id, value = chat_id
				$this->subscriptions[$conn->resourceId] = $data->chat_id;
				if ($data->user_id) {
					$this->user_ids[$conn->resourceId] = $data->user_id;
					$this->Player->setPlayerStatus($data->user_id, $data->player_status);

				}
				//Alert all people in global chat to update player list
				if ($data->user_id) {
					$this->emitCommandByChatId('updatePlayerList', Chat::Global_Chat_Id);
				}

				break;
			case "message":
				if (isset($this->subscriptions[$conn->resourceId])) {
					if (empty($this->user_ids[$conn->resourceId]))
						$data->msg->username = "Guest " . $conn->resourceId;
					//Save message in Database
					$saved = false;
					try{
						$saved = $this->Chat->sendMessage($this->subscriptions[$conn->resourceId], $data->msg);
					}catch(\Exception $e){
						echo dump($e);
					}

					//If message saved in db send it to all other users in same chat
					if ($saved) {
						$this->emitMessageByChatId($msg,$targetChat);
					}
				}
				break;
			case "closeLobby":
				//Send update lobby to everyone watching this lobby
				$this->emitCommandByChatId('closeLobby', $targetChat);
			case "updateLobby":
				//Send update lobby to everyone watching this lobby
				$this->emitCommandByChatId('updateLobby', $targetChat);
			case "updateLobbyList":
				$this->emitCommandByChatId('updateLobbyList', Chat::Global_Chat_Id);
				break;
			case "startLobby":
				//Send start lobby command to everyone watching this lobby that just started
				$this->emitCommandByChatId('startLobby', $targetChat);
				break;
			case "gameOver":
				//message contains command: 'gameOver', winner: 1 or 2, winner_name: 'asdsd'
				$this->emitMessageByChatId($msg,$targetChat);
				break;
			case "gameUpdate":
				$this->emitCommandByChatId('gameUpdate',$targetChat);
				break;
		}
	}

	public function onClose(ConnectionInterface $conn)
	{
		// The connection is closed, remove it, as we can no longer send it messages
		$this->clients->detach($conn);
		if (isset($this->user_ids[$conn->resourceId])) {
			//Set player to Offline
			$this->Player->removePlayer($this->user_ids[$conn->resourceId]);
			//Make player leave Lobby
			//$this->Lobby->leave($this->user_ids[$conn->resourceId]);
		}


		unset($this->users[$conn->resourceId]);
		unset($this->subscriptions[$conn->resourceId]);
		unset($this->user_ids[$conn->resourceId]);
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		echo "An error has occurred: {$e->getMessage()}\n";

		$conn->close();
	}


	//HELPER FUNCTIONS
	public function emitCommandByChatId($command, $chat_id)
	{
		foreach ($this->subscriptions as $socket_user_id => $user_chat_id) {
			if ($user_chat_id == $chat_id) {
				$this->users[$socket_user_id]->send(json_encode(array('command' => $command)));
			}
		}
	}
	public function emitMessageByChatId($msg, $chat_id){
		foreach ($this->subscriptions as $socket_user_id => $user_chat_id) {
			if ($user_chat_id == $chat_id) {
				$this->users[$socket_user_id]->send($msg);
			}
		}
	}

}