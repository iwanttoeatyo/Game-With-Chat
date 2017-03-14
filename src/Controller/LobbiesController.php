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
 *
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
	}

	/**
	 * View method
	 *
	 * @param string|null $id Lobby id.
	 * @return \Cake\Network\Response|null
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function view($id = null)
	{
		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		$lobby = $this->Lobby->getLobby($id);

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

	public function join($id = null)
	{
		if ($this->request->is('post')) {
			$user_id = $this->Auth->user('id');
			if (empty($user_id))
				//TODO add notification can only watch
				return $this->redirect(['action' => 'view', $id]);
			if ($this->Lobby->tryToJoin($user_id, $id))
				return $this->redirect(['action' => 'view', $id]);
			else
				//TODO add notification cant join lobby
				return $this->redirect(['controller' => 'Home', 'action' => 'index']);
		}
		return $this->redirect(['action' => 'view', $id]);
	}

	//Create new Lobby
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
				$lobby_id = $this->Lobby->create($this->Auth->user('id'));
			}
			if (isset($lobby_id)) {
				return $this->redirect(['action' => 'view', $lobby_id]);
			}

		}
		return $this->redirect(['controller' => 'Home', 'action' => 'index']);
	}


	public function leave($id = null)
	{
		$user_id = $this->Auth->user('id');
		if ($this->request->is('post') && isset($user_id)) {
			$this->Lobby->leave($user_id);
		}
		return $this->redirect(['controller' => 'Home', 'action' => 'index']);
	}


	public function start($id = null)
	{
		$user_id = $this->Auth->user('id');
		if ($this->request->is('post') && isset($user_id)) {
			$this->Lobby->start($id);
		} else
			return $this->redirect(['controller' => 'Home', 'action' => 'index']);
	}

	public function refreshLobby()
	{
		$this->autoRender = false;
		$lobby_id = $this->request->getData('id');
		if ($this->request->is('post')) {
			$lobby = $this->Lobby->getLobby($lobby_id);
			$data = [];
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
