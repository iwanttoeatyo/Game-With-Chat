<?php

namespace App\Controller;

use App\Model\Entity\Chat;
use App\Model\Entity\PlayerStatus;
use Cake\ORM\TableRegistry;
use DateTime;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Controller\Component\PlayerComponent;

/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
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
		$data = json_decode($msg);
		switch ($data->command) {
			case "joinChat":
				echo dump($data);
				//key = users connection id, value = chat_id
				$this->subscriptions[$conn->resourceId] = $data->chat_id;
				if ($data->user_id) {
					$this->user_ids[$conn->resourceId] = $data->user_id;
					$this->Player->addPlayer($data->user_id, $data->player_status);

				}

				//Alert all people in global chat to update player list
				if($data->user_id){
					foreach ($this->subscriptions as $socket_user_id => $chat_id) {
						if ($chat_id == Chat::Global_Chat_Id) {
							$this->users[$socket_user_id]->send(json_encode(array('command' => 'updatePlayerList')));
						}
					}
				}

				break;
			case "message":
				if (isset($this->subscriptions[$conn->resourceId])) {
					if (empty($this->user_ids[$conn->resourceId]))
						$data->msg->username = "Guest " . $conn->resourceId;
					//Save message in Database
					$saved = $this->Chat->sendMessage($this->subscriptions[$conn->resourceId], $data->msg);
					//If message saved in db send it to all other users in same chat
					if ($saved) {
						echo dump($data);
						$targetChat = $this->subscriptions[$conn->resourceId];
						foreach ($this->subscriptions as $socket_user_id => $chat_id) {
							if ($chat_id == $targetChat) {
								$this->users[$socket_user_id]->send(json_encode($data));
							}
						}
					}
				}
				break;
			case "updateLobby":
				echo dump($data);
				foreach ($this->subscriptions as $socket_user_id => $chat_id) {
					if ($chat_id == $data->chat_id) {
						$this->users[$socket_user_id]->send(json_encode(array('command' => 'updateLobby')));
					}
					if ($chat_id == Chat::Global_Chat_Id) {
						$this->users[$socket_user_id]->send(json_encode(array('command' => 'updateLobbyList')));
					}
				}

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


}