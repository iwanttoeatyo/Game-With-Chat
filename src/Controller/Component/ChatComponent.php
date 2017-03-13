<?php
namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Database\Query;
use Cake\ORM\TableRegistry;
use DateTime;

/**
 * Chat component
 *
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\MessagesTable $Messages
 *
 */
class ChatComponent extends Component
{

	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Messages = TableRegistry::get('Messages');
		$this->Chats = TableRegistry::get('Chats');
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

		//Reverse the order of messages so newest is on the bottom
		$messages = [];
		foreach ($results as $message){
			$messages[] = $message;
		}
		$messages = array_reverse($messages);

		return $messages;
	}

	public function sendMessage($chat_id,$msg){
		$message = $this->Messages->newEntity();
		$message->set('chat_id',$chat_id);
		$message->set('created_date',new DateTime('now'));
		$message->set('message',$msg->message);
		$message->set('username',$msg->username);
		if($this->Messages->save($message))
			return true;
	}

	public function create(){
		$chat = $this->Chats->newEntity();
		$chat->set('id',null);
		$this->Chats->save($chat);
		return $chat->id;
	}
}
