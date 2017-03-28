<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;

/**
 * Controller to display a checker's game.
 * 
 * 
 * Contains post request to forfeit game.
 * Contains ajax requests to update and get a game's game state.
 *
 *
 * @property \App\Model\Table\GamesTable $Games
 * @property \App\Controller\Component\GameComponent $Game
 * @property \App\Controller\Component\ChatComponent $Chat
 * @property \App\Controller\Component\LobbyComponent $Lobby
 */
class GamesController extends AppController
{

	/**
	 * Initialization hook method.
	 *
	 * Loads Chat, Lobby and Game components.
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Game');
		$this->loadComponent('Chat');
		$this->loadComponent('Lobby');
	}

	/**
	 * Display the view for the checker game
	 *
	 *
	 * Displays Template/Games/view.ctp
	 * Renders Template/Element/chat.ctp
	 * @param int|null $id
	 * @return \Cake\Http\Response|null
	 * @throws \Cake\Network\Exception\NotFoundException When the game could not be found by id
	 */
	public function view($id = null)
	{
		try{
			$game = $this->Game->getGame($id);
		}catch (\Exception $e){
			throw new NotFoundException(__('Game not found'));
		}
		
		//If game is ended redirect to home page
		if ($this->Game->isGameEnded($id))
			return $this->redirect(['controller' => 'Home', 'action' => 'index']);

		//Get current user's info from AuthComponent
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		//Get the lobby object associated with this game object
		$lobby = $this->Lobby->getLobby($game->get('lobby_id'));

		//Check if user is player1 or player2 in this game
		if (isset($user_id)) {
			if ($lobby->get('player1_user_id') == $user_id) {
				$is_player1 = true;
			} else if ($lobby->get('player2_user_id') == $user_id) {
				$is_player2 = true;
			}
		}

		//Get 10 most recent messages for this game's chat
		$messages = $this->Chat->getMessages($lobby->get('chat_id'));

		//Set page title
		$title = $lobby->get('name') . ' | ' . Configure::read('App.Name');;

		//Make these vars accessible in view.ctp
		$this->set(compact('title', 'messages', 'lobby', 'game',
			'username', 'user_id', 'is_player1', 'is_player2'));
	}


	/**
	 * Post request to forfeit a game.
	 *
	 *
	 * If the user is a player in a game they will forfeit the game.
	 * Expects a $game_id as POST data 'id'.
	 * @return \Cake\Http\Response|null
	 */
	public function forfeit()
	{
		//Don't render a page
		$this->autoRender = false;

		//Get current user's info from AuthComponent
		$game_id = $this->request->getData('id');
		$user_id = $this->Auth->user('id');

		//Accept post request only
		if ($this->request->is('post')) {
			//Make sure the user is a player in a game
			if ($this->Game->userIsPlayerInGame($game_id, $user_id)) {
				$resultJ = "null";
				//Try to make this user forfeit 
				if ($this->Game->tryForfeitGame($game_id, $user_id)) {
					$winnerInfo = $this->Game->getWinnerInfo($game_id);
					$resultJ = json_encode($winnerInfo);
				}
				$this->response->type('json');
				$this->response->body($resultJ);
			}
		}

		return $this->response;
	}


	/**
	 * Ajax Get Request to get the game_state of the game.
	 * 
	 * Expects an id as POST data<br>
	 * Returns JSON object<br>
	 * 
	 * @see \App\Model\Entity\Game
	 * @return \Cake\Http\Response|null
	 */
	public function getGameState()
	{
		$this->autoRender = false;
		if ($this->request->is('post')) {

			$game_id = $this->request->getData('id');
			$game_state = $this->Game->getGameState($game_id);
			$this->response->type('json');
			$this->response->body($game_state);
			return $this->response;
		}
	}

	/**
	 * Ajax Post Request to set the game_state of the game
	 *
	 * Updates the game_state and checks if there is a winner.
	 * Games is ended if there was a winner.
	 * Returns winner info as JSON if there was a winner.
	 *
	 * @see \App\Model\Entity\Game
	 * @return \Cake\Http\Response|null
	 */
	public function updateGameState()
	{
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$user_id = $this->Auth->user('id');
			if (isset($user_id)) {
				$json_game_state = $this->request->getData('game_state');
				$lobby = $this->Lobby->findLobbyByUserId($user_id);
				$game_id = $this->Game->findGameByLobbyId($lobby->get('id'))->get('id');
				$winnerInfo = [];
				$is_winner = $this->Game->updateGameStateByUserId($user_id, $json_game_state);
				if ($is_winner)
					$winnerInfo = $this->Game->getWinnerInfo($game_id);

				$resultJ = json_encode($winnerInfo);
				$this->response->type('json');
				$this->response->body($resultJ);
				return $this->response;
			}
		}
	}

}
