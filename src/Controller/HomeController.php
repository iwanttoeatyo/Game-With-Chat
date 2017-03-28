<?php

namespace App\Controller;

use App\Model\Entity\Chat;
use Cake\Core\Configure;

/**
 * Controller to display homepage.
 * Contains ajax calls to request lobby and player lists. As well
 * lobby and player info.
 * 
 * @property \App\Controller\Component\LobbyComponent $Lobby
 * @property \App\Controller\Component\ChatComponent $Chat
 * @property \App\Controller\Component\PlayerComponent $Player
 */

class HomeController extends AppController
{
	/**
	 * Initialization hook method.
	 *
	 * Loads Player, Chat and Lobby components.
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Player');
		$this->loadComponent('Chat');
		$this->loadComponent('Lobby');
	}

	/**
	 * Display homepage, with global chat and players/lobby lists.
	 * 
	 * Displays Template/Home/index.ctp
	 * Renders Template/Element/chat.ctp
	 * @return \Cake\Network\Response|null
	 */
	public function index()
	{
		$chat_id = Chat::GLOBAL_CHAT_ID;

		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		//get 10 recent messages for global chat
		$messages = $this->Chat->getMessages($chat_id);

		//get list of online players
		$players = $this->Player->getPlayerList();

		//get list of active lobbies
		$lobbies = $this->Lobby->getLobbyList();

		//set page title
		$title = Configure::read('App.Name');
		//make vars accessible in template
		$this->set(compact('title'));
		$this->set(compact('messages', 'lobbies', 'chat_id',
			'username', 'user_id', 'players'));
	}


	/**
	 * Ajax Get Request for Player List.
	 * 
	 * Renders Template/Element/player_list.ctp
	 * @return \Cake\Http\Response
	 */
	public function getPlayerList()
	{
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$players = $this->Player->getPlayerList();
			$this->set(compact('players'));
			$this->viewBuilder()->setTemplatePath('Element');
			$this->render('player_list', 'ajax');
		}
	}

	/**
	 *  Ajax Get Request for Lobby/Game List.
	 * 
	 * Renders Template/Element/lobby_list.ctp
	 * @return \Cake\Http\Response
	 */
	public function getLobbyList()
	{
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$lobbies = $this->Lobby->getLobbyList();
			$this->set(compact('lobbies'));
			$this->viewBuilder()->setTemplatePath('Element');
			$this->render('lobby_list', 'ajax');
		}
	}

	/**
	 * Ajax Get Request for Lobby/Game Info.
	 * 
	 * Renders Template/Element/lobby_info.ctp
	 * @return \Cake\Http\Response
	 */
	public function getLobbyInfo()
	{
		$user_id = $this->Auth->user('id');
		$id = $this->request->getData('id');
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$lobby = $this->Lobby->getLobby($id);
			$this->viewBuilder()->setTemplatePath('Element');

			//Check if the user is already joined in a another lobby
			$user_in_other_lobby = false;
			if(isset($user_id)){
				$other_lobby = $this->Lobby->findLobbyByUserId($user_id);
				if(isset($other_lobby) && $other_lobby->get('id') != $lobby->get('id'))
					$user_in_other_lobby = true;
			}

			$this->set(compact('lobby', 'user_id','user_in_other_lobby'));
			$this->render('lobby_info', 'ajax');
		}
	}

	/**
	 * Ajax Get Request for Player Info.
	 * 
	 * Renders Template/Element/player_info.ctp
	 * @return \Cake\Http\Response
	 */
	public function getPlayerInfo()
	{
		$id = $this->request->getData('id');
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$player = $this->Player->getPlayer($id);
			$this->viewBuilder()->setTemplatePath('Element');
			$this->set(compact('player'));
			$this->render('player_info', 'ajax');
		}
	}
}
