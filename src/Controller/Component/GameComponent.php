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
 */
class GameComponent extends Component
{

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Games = TableRegistry::get('Games');
	}

	public function findGameByLobbyId($lobby_id){
		$game = $this->Games->find()
			->where(['lobby_id' => $lobby_id])
			->contain(['Lobbies','GameStatuses'])
			->all()
			->first();
		return $game;
	}

	public function getGame($game_id){
		$game = $this->Games->get($game_id, [
			'contain' => ['Lobbies', 'GameStatuses']
		]);
		return $game;
	}

	public function createGameForLobby($lobby_id){
		$game = $this->Games->newEntity();
		$game->set('game_status_id',GameStatus::Active);
		$game->set('lobby_id',$lobby_id);
		$game->set('player_turn',1);
		$game->set('game_state', "{empty: 0}");
		$this->Games->save($game);
		return $game->get('id');
	}

//	public function setGameState($game_id,$JSON_game_state){
//		$game = $this->getGame($game_id);
//		$game->set('game_state',$JSON_game_state);
//		return $this->Games->save($game);
//	}
}
