<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Lobby;
use App\Model\Entity\Chat;
use App\Model\Entity\LobbyStatus;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\UnauthorizedException;

/**
 * Lobbies Controller
 *
 * @property \App\Model\Table\LobbiesTable $Lobbies
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Controller\Component\LobbyComponent $Lobby
 *
 */
class LobbiesController extends AppController
{

	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Chats');
		$this->loadModel('Users');
		$this->loadComponent('Lobby');
		$this->loadComponent('Chat');
	}

    /**
     * View method
     *
     * @param string|null $id Lobby id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {


		//get current user's username
		$username = $this->Auth->user('username');
		$user_id = $this->Auth->user('id');

		$lobby = $this->Lobby->getLobby($id);
		//get recent messages
		$messages = $this->Chat->getMessages($lobby->get('chat_id'));

		$title = $lobby->name;

		$this->set(compact('title', 'messages', 'lobby',
			'username', 'user_id'));
    }


    //Create new Lobby
    public function add()
    {
    //	debug(dump($this->request));
		if ($this->request->is('get')) {
			return $this->redirect(['controller'=>'Home', 'action' => 'index']);
		}
		$this->autoRender = false;

        if ($this->request->is('post')) {
        	//Make sure user is logged in.
			if(empty($this->Auth->user('id')))
				return $this->redirect(['controller'=>'Home', 'action' => 'index']);

			//If they are already in a lobby as a player then put them in that lobby
			$lobby = $this->Lobby->findUsersLobby($this->Auth->user('id'));
			if(isset($lobby)){
				$lobby_id = $lobby->id;
			}else{
				$lobby_id = $this->Lobby->create($this->Auth->user('id'));
			}

            if(isset($lobby_id)){
                return $this->redirect(['action' => 'view', $lobby_id]);
            }else{
				return $this->redirect(['controller'=>'Home', 'action' => 'index']);
			}

        }
    }


}
