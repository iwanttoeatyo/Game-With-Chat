<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

/**
 * Lobby component
 */
class LobbyComponent extends Component
{

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Lobbies = TableRegistry::get('Lobbies');

	}

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function getLobbyList(){
		$lobbies = $this->Lobbies->find()
			->contain(['LobbyStatuses'])
			->where(['lobby_status_id !=' => 3])
			->all();
		return $lobbies;
	}

	public function tryToJoin($user_id, $lobby_id){

	}

	public function create(){

	}

	public function getLobbyInfo($lobby_id){
		$lobby = $this->Lobbies->get($lobby_id);
		debug($lobby);
	}
}
