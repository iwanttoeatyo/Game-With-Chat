<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;

/**
 * Games Controller
 *
 * @property \App\Model\Table\GamesTable $Games
 * @property \App\Controller\Component\CheckersComponent $Checkers
 * @property \App\Controller\Component\GameComponent $Game
 * @property \App\Controller\Component\ChatComponent $Chat
 * @property \App\Controller\Component\LobbyComponent $Lobby
 */
class GamesController extends AppController
{

	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Checkers');
		$this->loadComponent('Game');
		$this->loadComponent('Chat');
		$this->loadComponent('Lobby');
	}

	public function view($id = null)
	{
		$game = $this->Game->getGame($id);
		//If game is ended redirect to home
		if ($this->Game->isGameEnded($id))
			return $this->redirect(['controller' => 'Home', 'action' => 'index']);

		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');


		$lobby = $this->Lobby->getLobby($game->get('lobby_id'));

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

		$title = $lobby->get('name') . ' | ' . Configure::read('App.Name');;

		$this->set(compact('title', 'messages', 'lobby', 'game',
			'username', 'user_id', 'is_player1', 'is_player2'));
	}


	public function forfeit()
	{
		$this->autoRender = false;
		$game_id = $this->request->getData('id');
		$user_id = $this->Auth->user('id');
		$resultJ = "null";
		if ($this->request->is('post')) {
			if ($this->Game->userIsPlayerInGame($game_id, $user_id)) {

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

	//expects game id as id
	//
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
				$winner = $this->Game->updateGameStateByUserId($user_id, $json_game_state);
				if($winner == 1){
					$winnerInfo = $this->Game->getWinnerInfo($game_id);
				}
				if($winner == 2){
					$winnerInfo = $this->Game->getWinnerInfo($game_id);
				}
				$resultJ = json_encode($winnerInfo);
				$this->response->type('json');
				$this->response->body($resultJ);
				return $this->response;
			}
		}
	}

}
