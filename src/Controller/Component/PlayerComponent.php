<?php
namespace App\Controller\Component;

use App\Model\Entity\PlayerStatus;
use App\Model\Entity\User;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Cache component
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\ScoresTable $Scores
 */
class PlayerComponent extends Component
{
	private $Users;

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Users = TableRegistry::get('Users');
		$this->Scores = TableRegistry::get('Scores');
	}

	/**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function getPlayerList(){
		$players = $this->Users->find('all')
			->contain(['PlayerStatuses','Scores'])
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
		$player->set('player_status_id',PlayerStatus::Offline);
		$this->Users->save($player);
	}

	public function getPlayerInfo($user_id){
		$player = $this->getPlayer($user_id);
		debug($player);
		return json_encode($player);
	}

	public function getPlayer($user_id){
		$player = $this->Users->get($user_id,[
			'contain' => ['PlayerStatuses','Scores']
		]);

		return $player;
	}

	public function createScore($user_id){
		$score = $this->Scores->newEntity();
		$score->set('user', $this->getPlayer($user_id));
		$score->set('win_count',0);
		$score->set('draw_count',0);
		$score->set('loss_count',0);
		$this->Scores->save($score);
	}
}
