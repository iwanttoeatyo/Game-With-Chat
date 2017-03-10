<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Home Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
 * @property \App\Model\Table\HomeTable $Home
 */
class HomeController extends AppController
{
	var $uses = false;



    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
    	$title = '';
		$this->set(compact('title'));
		$chat_id = 1;
		//todo get recent messages
		$messages = $this->$Messages->find('all')
			->where(['Messages.date_created >' => new DateTime('-1') ])
			->where(['chat_id' => $chat_id])
			->order(['date_created'] => 'ASC')
			->limit(10);

		//todo get playerlist
		//todo get lobby list




		$this->render('index');
    }


}
