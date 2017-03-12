<?php

namespace App\Controller;

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
class WebSocketController extends AppController implements MessageComponentInterface  {
	protected $clients;
	private $users;
	private $user_ids;
	private $subscriptions;
	private $Messages;

	public function initialize() {
		$this->loadComponent('Player');
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
		$this->users[$conn->resourceId] =  $conn;

	}

	public function onMessage(ConnectionInterface $conn, $msg) {
		$data = json_decode($msg);
		echo $data;
		switch ($data->command) {
			case "joinChat":
				//key = users connection id, value = chat_id
				$this->subscriptions[$conn->resourceId] = $data->chat_id;
				if($data->user_id){
					$this->user_ids[$conn->resourceId] = $data->user_id;
					$this->Player->addPlayer($data->user_id, $data->player_status);

				}

				//Emit update players
				foreach ($this->subscriptions as $user_id=>$chat_id) {
					if ($chat_id == 1) {
						$this->users[$user_id]->send(json_encode(array('command' => 'updatePlayers')));
					}
				}

				break;
			case "message":
				if (isset($this->subscriptions[$conn->resourceId])) {

					//Save message in Database
					$message = $this->Messages->newEntity();
					$message->chat_id = $this->subscriptions[$conn->resourceId];
					$message->created_date = new DateTime('now');
					$message->message = $data->msg->message;
					$message->username = $data->msg->username;
					//If message saved send it to all other users in same chat
					if($this->Messages->save($message)){
						$targetChat = $this->subscriptions[$conn->resourceId];
						foreach ($this->subscriptions as $user_id=>$chat_id) {
							if ($chat_id == $targetChat) {
								$this->users[$user_id]->send($msg);
							}
						}
					}


				}
		}
	}

	public function onClose(ConnectionInterface $conn) {
		// The connection is closed, remove it, as we can no longer send it messages
		$this->clients->detach($conn);
		if(isset($this->user_ids[$conn->resourceId]))
			$this->Player->removePlayer($this->user_ids[$conn->resourceId]);
		unset($this->users[$conn->resourceId]);
		unset($this->subscriptions[$conn->resourceId]);
		unset($this->user_ids[$conn->resourceId]);
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "An error has occurred: {$e->getMessage()}\n";

		$conn->close();
	}


}