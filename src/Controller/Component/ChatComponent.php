<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
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

    public function getMessages($chat_id){
		$messages = $this->Messages->find()
			->where(['chat_id ' => $chat_id])
			->order(['created_date' => 'ASC'])
			->limit(10)
			->all();
		return $messages;
	}
}
