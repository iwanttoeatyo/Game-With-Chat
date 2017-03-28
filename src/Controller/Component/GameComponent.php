<?php
namespace App\Controller\Component;

use App\Model\Entity\GameStatus;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Business logic component for GamesController
 * 
 * 
 * Contains CRUD operations and logic pertaining to game winning and forfeiting
 * 
 * @property \App\Model\Table\GamesTable $Games
 * @property \App\Model\Table\ScoresTable $Scores
 * @property \App\Controller\Component\PlayerComponent $Player
 * @property \App\Controller\Component\LobbyComponent $Lobby
 * @property Component\AuthComponent $Auth
 */
class GameComponent extends Component
{

	/**
	 * Other components used in this component
	 * 
	 * @var array
	 */
	public $components = ['Chat', 'Player', 'Lobby'];
	
	/**
	 * Initialization hook method.
	 *
	 * Loads Games and Scores database tables
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Games = TableRegistry::get('Games');
		$this->Scores = TableRegistry::get('Scores');
	}

	/**
	 * Returns a game object with lobby and GameStatus
	 * objects attached
	 * 
	 * @param $game_id
	 * @return \App\Model\Entity\Game
	 */
	public function getGame($game_id)
	{
		$game = $this->Games->get($game_id, [
			'contain' => ['Lobbies', 'GameStatuses']
		]);
		return $game;
	}

	/** 
	 * Returns the JSON game state
	 * 
	 * @param $game_id
	 * @return string
	 */
	public function getGameState($game_id)
	{
		$game = $this->getGame($game_id);
		return $game->get('game_state');
	}

	/**
	 * Returns winner 1 or 2 and winner name
	 * as array
	 * 
	 * @param $game_id
	 * @return array
	 */
	public function getWinnerInfo($game_id)
	{
		$winnerInfo = [];
		$game = $this->getGame($game_id);
		$lobby = $this->Lobby->getLobby($game->get('lobby_id'));
		if ($game->get('winner') == 1) {
			$winnerInfo['winner'] = 1;
			$winnerInfo['winner_name'] = $lobby->get('player1')->get('username');
		} else if ($game->get('winner') == 2) {
			$winnerInfo['winner'] = 2;
			$winnerInfo['winner_name'] = $lobby->get('player2')->get('username');
		}

		return $winnerInfo;
	}

	/**
	 * Returns game if it exists given associated lobby_id
	 * 
	 * @param $lobby_id
	 * @return \App\Model\Entity\Game 
	 */
	public function findGameByLobbyId($lobby_id)
	{
		$game = $this->Games->find()
			->where(['lobby_id' => $lobby_id])
			->contain(['Lobbies', 'GameStatuses'])
			->all()
			->first();
		return $game;
	}

	/**
	 * Creates a new Game and attaches it to a lobby
	 * 
	 * @param $lobby_id
	 * @return int game_id
	 */
	public function createGameForLobby($lobby_id)
	{
		$game = $this->Games->newEntity();
		$game->set('game_status_id', GameStatus::Active);
		$game->set('lobby_id', $lobby_id);
		$game->set('game_state', "{board:{playerTurn:1}}");
		$this->Games->save($game);
		return $game->get('id');
	}

	/**
	 * Updates Winner's win_count and Loser's loss_count
	 * in database.
	 * 
	 * @param $winner_id
	 * @param $loser_id
	 */
	public function updateScores($winner_id, $loser_id)
	{
		$winner = $this->Player->getPlayer($winner_id);
		$winner_score = $winner->get('score');
		$winner_score->set('win_count', $winner_score->get('win_count') + 1);
		$this->Scores->save($winner_score);
		$loser = $this->Player->getPlayer($loser_id);
		$loser_score = $loser->get('score');
		$loser_score->set('loss_count', $loser_score->get('loss_count') + 1);
		$this->Scores->save($loser_score);
	}

	/**
	 * Finds the game that a user is in and updates its game state
	 * If a player has captures 12 pieces then end game and return true
	 * 
	 * @param $user_id
	 * @param $json_game_state
	 * @return bool|int
	 */
	public function updateGameStateByUserId($user_id, $json_game_state)
	{
		$lobby = $this->Lobby->findLobbyByUserId($user_id);
		$winner = 0;
		if (isset($lobby)) {
			$game = $this->findGameByLobbyId($lobby->get('id'));
			$game->set('game_state', $json_game_state);
			$this->Games->save($game);
			//Also check for winner
			$winner = $this->checkForWinner($json_game_state);
			if ($winner == 1) {
				$this->setWinnerAndEndGame($game->get('id'), 1);
			}
			if ($winner == 2) {
				$this->setWinnerAndEndGame($game->get('id'), 2);
			}
		}
		return $winner;
	}

	/**
	 * Forfeit player from game if game is still active
	 * 
	 * @param $game_id
	 * @param $user_id
	 * @return bool
	 */
	public function tryForfeitGame($game_id, $user_id)
	{
		if ($this->isGameActive($game_id)) {
			$game = $this->getGame($game_id);
			$lobby = $game->get('lobby');
			//Player who didn't forfeit is the winner
			if ($lobby->get('player1_user_id') == $user_id) {
				//other player won aka player 2
				$this->setWinnerAndEndGame($game_id, 2);
			}
			if ($lobby->get('player2_user_id') == $user_id) {
				$this->setWinnerAndEndGame($game_id, 1);
			}
			return true;
		}
		return false;
	}

	/**
	 * Set game's winner, update winner's and loser'sScores,
	 * close lobby
	 * 
	 * @param int $game_id
	 * @param int $winner
	 */
	public function setWinnerAndEndGame($game_id, $winner)
	{
		$game = $this->getGame($game_id);
		$lobby = $this->Lobby->getLobby($game->get('lobby_id'));
		$winner_id = 0;
		$loser_id = 0;

		$game->set('game_status_id', GameStatus::Ended);

		if ($winner == 1) {
			$game->set('winner', 1);
			$winner_id = $lobby->get('player1_user_id');
			$loser_id = $lobby->get('player2_user_id');
		}
		if ($winner == 2) {
			$game->set('winner', 2);
			$winner_id = $lobby->get('player2_user_id');
			$loser_id = $lobby->get('player1_user_id');
		}
		$this->Games->save($game);
		$this->Lobby->closeLobby($game->get('lobby_id'));
		$this->updateScores($winner_id, $loser_id);
	}

	/**
	 * Return player number if player has captured 12 pieces
	 * 
	 * @param string $json_game_state
	 * @return int
	 */
	public function checkForWinner($json_game_state)
	{
		$game_state = json_decode($json_game_state);
		$player1_captured_pieces = $game_state->captured[1];
		$player2_captured_pieces = $game_state->captured[2];
		if ($player1_captured_pieces == 12) {
			return 1;
		}
		if ($player2_captured_pieces == 12) {
			return 2;
		}
		return 0;
	}

	/**
	 * Return true if user is player 1 or player 2 in game
	 * 
	 * @param int $game_id
	 * @param int $user_id
	 * @return bool
	 */
	public function userIsPlayerInGame($game_id, $user_id)
	{
		$game = $this->getGame($game_id);
		$lobby = $game->get('lobby');
		return (($lobby->get('player1_user_id') == $user_id) ||
			($lobby->get('player2_user_id') == $user_id));
	}

	/**	
	 * Return true if GameStatus is Active
	 * @see \App\Model\Entity\GameStatus 
	 * 
	 * @param $game_id
	 * @return bool
	 */
	public function isGameActive($game_id)
	{
		$game = $this->getGame($game_id);
		return ($game->get('game_status_id') == GameStatus::Active);
	}

	/**
	 * Return true if GameStatus is Ended
	 * @see \App\Model\Entity\GameStatus
	 * 
	 * @param $game_id
	 * @return bool
	 */
	public function isGameEnded($game_id)
	{
		$game = $this->getGame($game_id);
		return ($game->get('game_status_id') == GameStatus::Ended);
	}

}
