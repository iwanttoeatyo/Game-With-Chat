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

	//expects game id as id
	//
	public function getGameState()
	{
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');

		$game_id = $this->request->getParam('id');

		//TODO get game state from game entity
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
					$game = $this->Game->getGame($game_id);
					$lobby = $this->Lobby->getLobby($game->get('lobby_id'));
					$winner = $game->get('winner');
					$winner_name = "?";
					if ($winner == 1) {
						$winner_name = $lobby->get('player1')->get('username');
					} else if ($winner == 2) {
						$winner_name = $lobby->get('player2')->get('username');
					}
					$resultJ = json_encode(array($winner, $winner_name));
				}
				$this->response->type('json');
				$this->response->body($resultJ);
			}
		}

		return $this->response;
	}


}
