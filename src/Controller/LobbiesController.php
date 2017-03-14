<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Lobby;
use App\Model\Entity\Chat;
use App\Model\Entity\LobbyStatus;
use Cake\Core\Configure;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\UnauthorizedException;

/**
 * Lobbies Controller
 *
 * @property \App\Model\Table\LobbiesTable $Lobbies
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Controller\Component\LobbyComponent $Lobby
 * @property \App\Controller\Component\GameComponent $Game
 * @property \App\Controller\Component\ChatComponent $Chat
 */
class LobbiesController extends AppController
{

	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Chats');
		$this->loadModel('Users');
		$this->loadComponent('Lobby');
		$this->loadComponent('Chat');
		$this->loadComponent('Game');
	}


	public function view($id = null)
	{
		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		$lobby = $this->Lobby->getLobby($id);
		if($lobby->get('lobby_status_id') == LobbyStatus::Started){
			$game = $this->Game->findGameByLobbyId($lobby->get('id'));
			return $this->redirect(['controller' => 'Games', 'action' => 'view', $game->get('id')]);
		}

		//check if user is player
		if (isset($user_id)) {
			if ($lobby->get('player1_user_id') == $user_id) {
				$is_player1 = true;
			} else if ($lobby->get('player2_user_id') == $user_id) {
				$is_player2 = true;
			}
		}
		//get recent messages
		$messages = $this->Chat->getMessages($lobby->get('chat_id'));

		$title = $lobby->name . ' | ' . Configure::read('App.Name');;

		$this->set(compact('title', 'messages', 'lobby',
			'username', 'user_id', 'is_player1', 'is_player2'));
	}

	//If logged in and lobby is open and user isn't in a lobby
	//then this user will join this lobby.
	//All clients will view lobby.
	public function join($id = null)
	{
		if ($this->request->is('post')) {
			$user_id = $this->Auth->user('id');
			if (empty($user_id))
				//TODO add notification can only watch
				return $this->redirect(['action' => 'view', $id]);
			if ($this->Lobby->tryToAddPlayer2ToLobby($user_id, $id))
				return $this->redirect(['action' => 'view', $id]);
			else
				//TODO add notification cant join lobby
				return $this->redirect(['controller' => 'Home', 'action' => 'index']);
		}
		return $this->redirect(['action' => 'view', $id]);
	}

	//Create new Lobby and redirect to it
	public function add()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			//Make sure user is logged in.
			if (empty($this->Auth->user('id')))
				return $this->redirect(['controller' => 'Home', 'action' => 'index']);

			//If they are already in a lobby as a player then put them in that lobby
			$lobby = $this->Lobby->findLobbyByUserId($this->Auth->user('id'));
			if (isset($lobby)) {
				$lobby_id = $lobby->id;
			} else {
				$lobby_id = $this->Lobby->createLobby($this->Auth->user('id'));
			}
			if (isset($lobby_id)) {
				return $this->redirect(['action' => 'view', $lobby_id]);
			}

		}
		return $this->redirect(['controller' => 'Home', 'action' => 'index']);
	}


	//Leave lobby
	public function leave()
	{
		$user_id = $this->Auth->user('id');
		if ($this->request->is('post') && isset($user_id)) {
			$this->Lobby->leaveLobby($user_id);
		}
		return $this->redirect(['controller' => 'Home', 'action' => 'index']);
	}


	public function start($id = null)
	{
		$user_id = $this->Auth->user('id');
		if ($this->request->is('post') && isset($user_id)) {
			if ($this->Lobby->startLobby($id)) {
				$game = $this->Lobby->getGameByLobbyId($id);
				debug(dump($game));
				return $this->redirect(['controller' => 'Games', 'action' => 'view', $game->id]);
			}
		}
		return $this->redirect(['action' => 'view', $id]);
	}


	//Returns the player2s name and lobby status
	//from ajax request in lobby view.
	public function refreshLobby()
	{
		$this->autoRender = false;
		$lobby_id = $this->request->getData('id');
		if ($this->request->is('post')) {
			$lobby = $this->Lobby->getLobby($lobby_id);
			$data['lobby_status'] = $lobby->get('lobby_status')->get('lobby_status');
			if ($lobby->get('player2'))
				$data['player2_name'] = $lobby->get('player2')->get('username');
			$resultJ = json_encode($data);
			$this->response->type('json');
			$this->response->body($resultJ);
			return $this->response;
		}
	}
}
