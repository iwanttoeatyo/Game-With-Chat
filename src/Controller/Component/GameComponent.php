<?php
namespace App\Controller\Component;

use App\Model\Entity\GameStatus;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

/**
 * Game component
 *
 * @property \App\Model\Table\GamesTable $Games
 * @property \App\Model\Table\ScoresTable $Scores
 * @property \App\Controller\Component\PlayerComponent $Player
 *  @property \App\Controller\Component\LobbyComponent $Lobby
 * @property Component\AuthComponent $Auth
 */
class GameComponent extends Component
{

	public $components = ['Chat', 'Player', 'Lobby'];

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Games = TableRegistry::get('Games');
		$this->Scores = TableRegistry::get('Scores');
	}

	public function findGameByLobbyId($lobby_id)
	{
		$game = $this->Games->find()
			->where(['lobby_id' => $lobby_id])
			->contain(['Lobbies', 'GameStatuses'])
			->all()
			->first();
		return $game;
	}

	public function getGame($game_id)
	{
		$game = $this->Games->get($game_id, [
			'contain' => ['Lobbies', 'GameStatuses']
		]);
		return $game;
	}

	public function createGameForLobby($lobby_id)
	{
		$game = $this->Games->newEntity();
		$game->set('game_status_id', GameStatus::Active);
		$game->set('lobby_id', $lobby_id);
		$game->set('game_state', "{empty: 0}");
		$this->Games->save($game);
		return $game->get('id');
	}


	public function tryForfeitGame($game_id, $user_id)
	{
		if($this->isGameActive($game_id)){
			$game = $this->getGame($game_id);
			$game->set('game_status_id',GameStatus::Ended);
			$lobby = $game->get('lobby');
			//Player who didn't forfeit is the winner
			if($lobby->get('player1_user_id') == $user_id ){
				$game->set('winner',2);
				$winner_id = $lobby->get('player2_user_id');
			}else{
				$game->set('winner',1);
				$winner_id = $lobby->get('player1_user_id');
			}
			$this->Games->save($game);
			$this->Lobby->closeLobby($game->get('lobby_id'));
			$this->updateScores($winner_id,$user_id);
			return true;
		}
		return false;
	}

	public function userIsPlayerInGame($game_id, $user_id)
	{
		$game = $this->getGame($game_id);
		$lobby = $game->get('lobby');
		return (($lobby->get('player1_user_id') == $user_id) ||
			($lobby->get('player2_user_id') == $user_id));
	}

	public function updateScores($winner_id, $loser_id){
		$winner = $this->Player->getPlayer($winner_id);
		$winner_score = $winner->get('score');
		$winner_score->set('win_count',$winner_score->get('win_count') + 1);
		$this->Scores->save($winner_score);
		$loser = $this->Player->getPlayer($loser_id);
		$loser_score = $loser->get('score');
		$loser_score->set('loss_count',$winner_score->get('loss_count') + 1);
		$this->Scores->save($loser_score);
	}

	public function isGameActive($game_id){
		$game = $this->getGame($game_id);
		return ($game->get('game_status_id') == GameStatus::Active);
	}

	public function isGameEnded($game_id)
	{
		$game = $this->getGame($game_id);
		return ($game->get('game_status_id') == GameStatus::Ended);
	}
//	public function setGameState($game_id,$JSON_game_state){
//		$game = $this->getGame($game_id);
//		$game->set('game_state',$JSON_game_state);
//		return $this->Games->save($game);
//	}
}
