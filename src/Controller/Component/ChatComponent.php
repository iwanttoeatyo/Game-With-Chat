<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use DateTime;

/**
 * Business logic related to Chat and Messages
 * Creates new Chats and inserts and select Messages
 *
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\MessagesTable $Messages
 *
 */
class ChatComponent extends Component
{
	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [];

	/**
	 * Initialization hook method.
	 *
	 * Loads Chats and Messages database tables
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config)
	{
		parent::initialize($config);
		$this->Messages = TableRegistry::get('Messages');
		$this->Chats = TableRegistry::get('Chats');
	}

	/**
	 * Gets 10 most recent messages and reverses them
	 * so the most recent message is at the end of the array.
	 * Useful for displaying chat with recent message at bottom.
	 * 
	 * @param int $chat_id
	 * @return array
	 */
	public function getMessages($chat_id)
	{
		$results = $this->Messages->find()
			->where(['chat_id ' => $chat_id])
			->order(['created_date' => 'DESC'])
			->limit(10)
			->all();

		//Reverse the order of messages so newest is on the bottom
		$messages = [];
		foreach ($results as $message) {
			$messages[] = $message;
		}
		$messages = array_reverse($messages);
		return $messages;
	}

	/**
	 * Inserts msg object into database.
	 * Example msg for chat message<br>
	 * ```(	[username] => a
	 *	   	[message] => yes )```
	 * 
	 * @param int $chat_id
	 * @param \stdClass $msg
	 * @return bool
	 */
	public function createMessage($chat_id, $msg)
	{
		$message = $this->Messages->newEntity();
		$message->set('chat_id', $chat_id);
		$message->set('created_date', new DateTime('now'));
		$message->set('message', $msg->message);
		$message->set('username', $msg->username);
		if ($this->Messages->save($message))
			return true;
	}

	/**
	 * Inserts an empty Chat into database.
	 * Id is auto generated
	 * 
	 * @return int
	 */
	public function createChat()
	{
		$chat = $this->Chats->newEntity();
		$chat->set('id', null);
		$this->Chats->save($chat);
		return $chat->id;
	}
}
