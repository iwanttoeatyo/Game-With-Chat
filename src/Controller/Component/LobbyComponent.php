<?php
namespace App\Controller\Component;

use App\Model\Entity\Lobby;
use App\Model\Entity\LobbyStatus;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

/**
 * Lobby component
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\LobbiesTable $Lobbies
 * @property \App\Controller\Component\ChatComponent $Chat
 */
class LobbyComponent extends Component
{

	// The other component your component uses
	public $components = ['Chat'];

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Lobbies = TableRegistry::get('Lobbies');
		$this->Users = TableRegistry::get('Users');

	}

	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [];

	public function getLobbyList()
	{
		$lobbies = $this->Lobbies->find()
			->contain(['LobbyStatuses'])
			->where(['lobby_status_id !=' => 3])
			->all();
		return $lobbies;
	}

	public function tryToJoin($user_id, $lobby_id)
	{

	}

	public function create($user_id)
	{
		$lobby = $this->Lobbies->newEntity();
		$chat_id = $this->Chat->create();
		$lobby->set('chat_id',$chat_id);
		$lobby->set('is_locked', Lobby::Unlocked);
		$lobby->set('lobby_status_id',LobbyStatus::Open);
		$player1 = $this->Users->get($user_id);
		$lobby->set('player1',$player1);
		$lobby->set('name', $player1->username.'\'s Lobby');
		$this->Lobbies->save($lobby);
		return $lobby->id;
	}

	public function getLobby($lobby_id)
	{
		$lobby = $this->Lobbies->get($lobby_id, [
			'contain' => ['Player1', 'Player2', 'LobbyStatuses']
		]);

		return $lobby;
	}

	public function leave($user_id)
	{
		$lobby = $this->findUsersLobby($user_id);
		if ($lobby){
			if($lobby->player1_user_id == $user_id){
				//Lock lobby so nothing messes up when closing it
				$this->lock($lobby->id);
				$this->close($lobby->id);
			}else if ($lobby->player2_user_id == $user_id){
				$lobby->set('player2_user_id',null);
				//Open lobby up if its not locked
				if(!$lobby->get('is_locked'))
					$lobby->set('lobby_status_id',LobbyStatus::Open);
				$this->Lobbies->save($lobby);
			}
		}
	}

	public function close($id){
		$lobby = $this->Lobbies->get($id);
		$lobby->set('lobby_status_id',LobbyStatus::Closed);
		if($this->Lobbies->save($lobby))
			echo "Lobby ".$lobby->id." Closed";

	}

	public function lock($id){
		$lobby = $this->Lobbies->get($id);
		$lobby->set('is_locked',Lobby::Locked);
		$this->Lobbies->save($lobby);
	}

	public function findUsersLobby($user_id){
		$lobby = $this->Lobbies->find()
			->where(['lobby_status_id !=' => LobbyStatus::Closed])
			->where(['player1_user_id' => $user_id])
			->orWhere(['player2_user_id' => $user_id])
			->all()
			->first();
		return $lobby;
	}
}
