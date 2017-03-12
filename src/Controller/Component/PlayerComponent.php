<?php
namespace App\Controller\Component;

use App\Model\Entity\User;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Cache component
 */
class PlayerComponent extends Component
{
	private $Users;

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Users = TableRegistry::get('Users');

	}

	/**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function getPlayerList(){
		$players = $this->Users->find('all')
			->contain(['PlayerStatuses'])
			->where(['player_status_id >' => '0'])
			->all();
		return $players;
	}

	public function addPlayer($user_id, $player_status){
		$player = $this->getPlayer($user_id);
		$player->set('player_status_id',$player_status);
		$this->Users->save($player);
	}

	public function removePlayer($user_id){
		$player = $this->getPlayer($user_id);
		$player->set('player_status_id',0);
		$this->Users->save($player);
	}

	public function getPlayerInfo($user_id){
		$player = $this->getPlayer($user_id);
		debug($player);
		return json_encode($player);
	}

	public function getPlayer($user_id){
		$player = $this->Users->get($user_id);
		return $player;
	}
}
