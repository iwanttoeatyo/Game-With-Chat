<?php
namespace App\Controller;

use App\Model\Entity\LobbyStatus;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;

/**
 * Controller to display a lobby.
 * 
 * Contains post request to join, add(create), leave and start a lobby.
 * Contains ajax requests to get updated information about players in a lobby.
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

	/**
	 *	Loads Models and components for this controller to use
	 */
	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Chats');
		$this->loadModel('Users');
		$this->loadComponent('Lobby');
		$this->loadComponent('Chat');
		$this->loadComponent('Game');
	}

	/**
	 * Displays a lobby view
	 *
	 * Displays Template/Lobbies/view.ctp<br>
	 * Renders Template/Element/chat.ctp
	 * @param int|null $id
	 * @return \Cake\Http\Response
	 * @throws \Cake\Network\Exception\NotFoundException When the lobby could not be found by id
	 */
	public function view($id = null)
	{
		try{
			$lobby = $this->Lobby->getLobby($id);
		}catch (\Exception $e){
			throw new NotFoundException(__('Lobby not found'));
		}
		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		//If lobby is started redirect to GamesController view
		//if closed redirect home
		if ($lobby->get('lobby_status_id') == LobbyStatus::Started) {
			$game = $this->Game->findGameByLobbyId($lobby->get('id'));
			return $this->redirect(['controller' => 'Games', 'action' => 'view', $game->get('id')]);
		} else if ($lobby->get('lobby_status_id') == LobbyStatus::Closed) {
			return $this->redirect(['controller' => 'Home', 'action' => 'index']);
		}
		//check if user is player
		if (isset($user_id)) {
			if ($lobby->get('player1_user_id') == $user_id) {
				$is_player1 = true;
			} else if ($lobby->get('player2_user_id') == $user_id) {
				$is_player2 = true;
			}
		}
		//get 10 most recent messages
		$messages = $this->Chat->getMessages($lobby->get('chat_id'));

		//set page title
		$title = $lobby->name . ' | ' . Configure::read('App.Name');;

		//make vars accessible in template
		$this->set(compact('title', 'messages', 'lobby',
			'username', 'user_id', 'is_player1', 'is_player2'));
	}

	/**
	 * Tries to add this user to the lobby given by the id
	 * If lobby is open and unlocked user is added. If user is not
	 * add they are redirected to Home/index
	 * 
	 * @param int|null $id 
	 * @return \Cake\Http\Response|null
	 */
	public function join($id = null)
	{
		if ($this->request->is('post')) {
			$user_id = $this->Auth->user('id');
			if (empty($user_id))

				return $this->redirect(['action' => 'view', $id]);
			if ($this->Lobby->tryToAddPlayer2ToLobby($user_id, $id))
				return $this->redirect(['action' => 'view', $id]);
			else
				return $this->redirect(['controller' => 'Home', 'action' => 'index']);
		}
		return $this->redirect(['action' => 'view', $id]);
	}


	/**
	 * 	Create new Lobby and redirect to user it
	 * 
	 * @return \Cake\Http\Response|null
	 */
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


	
	/**
	 * Remove's a user a from the lobby they have joined
	 * 
	 * @return \Cake\Http\Response|null
	 */
	public function leave()
	{
		$user_id = $this->Auth->user('id');
		if ($this->request->is('post') && isset($user_id)) {
			$this->Lobby->leaveLobby($user_id);
		}
		return $this->redirect(['controller' => 'Home', 'action' => 'index']);
	}


	/**
	 * Starts a checkers game from a full lobby
	 * 
	 * @param null $id
	 * @return \Cake\Http\Response|null
	 */
	public function start($id = null)
	{
		$user_id = $this->Auth->user('id');
		if ($this->request->is('post') && isset($user_id)) {
			if ($this->Lobby->startLobby($id)) {
				$lobby = $this->Lobby->getLobby($id);
				$game = $this->Game->findGameByLobbyId($lobby->get('id'));
				return $this->redirect(['controller' => 'Games', 'action' => 'view', $game->id]);
			}
		}
		return $this->redirect(['action' => 'view', $id]);
	}



	/**
	 * Ajax post request for updated lobby information.
	 * 
	 * Returns the player2's name and lobby status as JSON
	 * @return \Cake\Http\Response|null
	 */
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
