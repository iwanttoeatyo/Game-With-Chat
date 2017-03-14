<?php
namespace App\Controller;

/**
 * Home Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
 * @property \App\Model\Table\LobbiesTable $Lobbies
 * @property \App\Controller\Component\LobbyComponent $Lobby
 * @property \App\Controller\Component\ChatComponent $Chat
 * @property \App\Controller\Component\PlayerComponent $Player
 */
class HomeController extends AppController
{
	var $uses = false;


	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Player');
		$this->loadComponent('Chat');
		$this->loadComponent('Lobby');
		$this->loadModel('Messages');
		$this->loadModel('Lobbies');
	}

	/**
	 * Index method
	 *
	 * @return \Cake\Network\Response|null
	 */
	public function index()
	{

		$chat_id = 1; //global chat id

		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		//get recent messages
		$messages = $this->Chat->getMessages($chat_id);

		$players = $this->Player->getPlayerList();

		$lobbies = $this->Lobby->getLobbyList();
		//get lobby list


		$title = 'URGame';
		$this->set(compact('title'));
		$this->set(compact('messages', 'lobbies', 'chat_id',
			'username', 'user_id', 'players'));
		$this->render('index');
	}


	public function getPlayerList(){
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$players = $this->Player->getPlayerList();
			$this->set(compact('players'));
			$this->viewBuilder()->setTemplatePath('Element');
			$this->render('player_list','ajax');
		}
	}

	public function getLobbyList(){
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$lobbies = $this->Lobby->getLobbyList();
			$this->set(compact('lobbies'));
			$this->viewBuilder()->setTemplatePath('Element');
			$this->render('lobby_list','ajax');
		}
	}

	public function getLobbyInfo(){
		$user_id = $this->Auth->user('id');
		$id = $this->request->getData('id');
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$lobby = $this->Lobby->getLobby($id);
			$this->viewBuilder()->setTemplatePath('Element');
			$this->set(compact('lobby', 'user_id'));
			$this->render('lobby_info','ajax');
		}
	}

	public function getPlayerInfo(){
		$id = $this->request->getData('id');
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$player = $this->Player->getPlayer($id);
			$this->viewBuilder()->setTemplatePath('Element');
			$this->set(compact('player'));
			$this->render('player_info','ajax');
		}
	}
}
