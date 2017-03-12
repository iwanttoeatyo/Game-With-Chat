<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Database\Query;
use Cake\ORM\TableRegistry;

/**
 * Chat component
 */
class ChatComponent extends Component
{

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Messages = TableRegistry::get('Messages');
	}

	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [];

	public function getMessages($chat_id)
	{
		$results = $this->Messages->find()
			->where(['chat_id ' => $chat_id])
			->order(['created_date' => 'DESC'])
			->limit(10)
			->all();

		$messages = [];
		foreach ($results as $message){
			$messages[] = $message;
		}
		$messages = array_reverse($messages);

		return $messages;
	}
}
