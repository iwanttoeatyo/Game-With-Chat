<?php
namespace App\Controller\Component;

use App\Model\Entity\PlayerStatus;
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

	/**
	 * Initialization hook method.
	 *
	 * Loads Users and Scores database tables
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Users = TableRegistry::get('Users');
		$this->Scores = TableRegistry::get('Scores');
	}

	/**
	 * Returns a user object with Player status and scores objects
	 * attached
	 * 
	 * @param $user_id
	 * @return \App\Model\Entity\User
	 */
	public function getPlayer($user_id)
	{
		$player = $this->Users->get($user_id, [
			'contain' => ['PlayerStatuses', 'Scores']
		]);

		return $player;
	}

	/**
	 * Returns user object as JSON
	 * 
	 * @param $user_id
	 * @return string
	 */
	public function getPlayerInfo($user_id)
	{
		$player = $this->getPlayer($user_id);
		debug($player);
		return json_encode($player);
	}

	/**
	 * Returns list of all users that are not offline
	 * 
	 * @return \Cake\Datasource\ResultSetInterface
	 */
	public function getPlayerList()
	{
		$players = $this->Users->find('all')
			->contain(['PlayerStatuses', 'Scores'])
			->where(['player_status_id >' => '0'])
			->all();
		return $players;
	}

	/**
	 * sets a user's player status
	 * 
	 * @param $user_id
	 * @param $player_status
	 */
	public function setPlayerStatus($user_id, $player_status)
	{
		$player = $this->getPlayer($user_id);
		$player->set('player_status_id', $player_status);
		$this->Users->save($player);
	}

	/**
	 * sets a user's player status to offline
	 * 
	 * @param $user_id
	 */
	public function setPlayerOffline($user_id)
	{
		$player = $this->getPlayer($user_id);
		$player->set('player_status_id', PlayerStatus::Offline);
		$this->Users->save($player);
	}

	/**
	 * Creates a new score for a user and sets everything to zero
	 * 
	 * @param $user_id
	 */
	public function createScore($user_id)
	{
		$score = $this->Scores->newEntity();
		$score->set('user', $this->getPlayer($user_id));
		$score->set('win_count', 0);
		$score->set('draw_count', 0);
		$score->set('loss_count', 0);
		$this->Scores->save($score);
	}
}
