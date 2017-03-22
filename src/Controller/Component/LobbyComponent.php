<?php
namespace App\Controller\Component;

use App\Model\Entity\Lobby;
use App\Model\Entity\LobbyStatus;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Lobby component
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\LobbiesTable $Lobbies
 * @property \App\Controller\Component\ChatComponent $Chat
 * @property \App\Controller\Component\PlayerComponent $Player
 * @property \App\Controller\Component\GameComponent $Game
 */
class LobbyComponent extends Component
{
	// The other component your component uses
	public $components = ['Chat', 'Player', 'Game'];

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Lobbies = TableRegistry::get('Lobbies');
		$this->Users = TableRegistry::get('Users');
	}

	public function getLobby($lobby_id)
	{
		$lobby = $this->Lobbies->get($lobby_id, [
			'contain' => ['Player1', 'Player2', 'LobbyStatuses',]
		]);
		return $lobby;
	}

	public function getLobbyList()
	{
		$lobbies = $this->Lobbies->find()
			->contain(['LobbyStatuses'])
			->where(['lobby_status_id !=' => LobbyStatus::Closed])
			->all();
		return $lobbies;
	}

	//Returns lobby that is not closed
	public function findLobbyByUserId($user_id)
	{
		$lobby = $this->Lobbies->find()
			->where(['player1_user_id' => $user_id])
			->orWhere(['player2_user_id' => $user_id])
			->where(['lobby_status_id !=' => LobbyStatus::Closed])
			->all()
			->first();
		return $lobby;
	}


	public function createLobby($user_id)
	{
		$lobby = $this->Lobbies->newEntity();
		$chat_id = $this->Chat->createChat();
		$lobby->set('chat_id', $chat_id);
		$lobby->set('is_locked', Lobby::Unlocked);
		$lobby->set('lobby_status_id', LobbyStatus::Open);
		$player1 = $this->Users->get($user_id);
		$lobby->set('player1', $player1);
		$lobby->set('name', $player1->username . '\'s Lobby');
		$this->Lobbies->save($lobby);
		return $lobby->get('id');
	}

	public function startLobby($lobby_id)
	{
		$lobby = $this->getLobby($lobby_id);
		if ($this->isLobbyUnlocked($lobby_id)) {
			if ($lobby->get('lobby_status_id') == LobbyStatus::Full) {
				$game_id = $this->Game->createGameForLobby($lobby_id);
				$lobby->set('lobby_status_id', LobbyStatus::Started);
				$lobby_name = $lobby->get('player1')->get('username') . ' vs ' . $lobby->get('player2')->get('username');
				$lobby->set('name', $lobby_name);
				return $this->Lobbies->save($lobby);
			}
		}
		return false;
	}

	public function closeLobby($lobby_id)
	{
		$lobby = $this->getLobby($lobby_id);
		$lobby->set('lobby_status_id', LobbyStatus::Closed);
		return $this->Lobbies->save($lobby);
	}

	public function leaveLobby($user_id)
	{
		$lobby = $this->findLobbyByUserId($user_id);
		if ($lobby) {
			if ($lobby->player1_user_id == $user_id) {
				//Lock lobby so nothing messes up when closing it
				$this->lockLobby($lobby->id);
				$this->closeLobby($lobby->id);
			} else if ($lobby->player2_user_id == $user_id) {
				$lobby->set('player2_user_id', null);
				//Open lobby up if its not locked
				if ($this->isLobbyUnlocked($lobby->id))
					$lobby->set('lobby_status_id', LobbyStatus::Open);
				$this->Lobbies->save($lobby);
			}
		}
	}

	public function tryToAddPlayer2ToLobby($user_id, $lobby_id)
	{
		//if user already joined a lobby
		$lobby = $this->findLobbyByUserId($user_id);
		if (isset($lobby))
			return true;

		if ($this->isLobbyOpen($lobby_id)) {
			if ($this->isLobbyUnlocked($lobby_id)) {
				$this->lockLobby($lobby_id);
				return ($this->addPlayer2ToLobby($user_id, $lobby_id));
			}
		}
		return false;
	}

	private function addPlayer2ToLobby($user_id, $lobby_id)
	{
		$lobby = $this->getLobby($lobby_id);
		$player = $this->Player->getPlayer($user_id);
		$lobby->set('player2', $player);
		$lobby->set('lobby_status_id', LobbyStatus::Full);
		$lobby->set('is_locked', Lobby::Unlocked);
		return $this->Lobbies->save($lobby);
	}

	public function lockLobby($lobby_id)
	{
		$lobby = $this->getLobby($lobby_id);
		$lobby->set('is_locked', Lobby::Locked);
		return $this->Lobbies->save($lobby);
	}

	public function isLobbyOpen($lobby_id)
	{
		$lobby = $this->getLobby($lobby_id);
		return ($lobby->get('lobby_status_id') == LobbyStatus::Open);
	}

	public function isLobbyUnlocked($lobby_id)
	{
		$lobby = $this->getLobby($lobby_id);
		return !($lobby->get('is_locked'));
	}

}
