<?php
namespace App\Controller;

use App\Model\Entity\PlayerStatus;
use Cake\Core\Configure;

/**
 * Controller for login/logout and user registration
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\PlayerStatusesTable $PlayerStatuses
 * @property \App\Controller\Component\PlayerComponent $Player
 */
class UsersController extends AppController
{
	
	/**
	 * Initialization hook method.
	 *
	 * Loads Player components
	 *
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Player');
	}

	/**
	 * Displays Login page and redirects logged in users to Home/index
	 * 
	 * Displays Template/Users/login.ctp
	 * @return \Cake\Http\Response|null
	 */
	public function login()
	{
		if ($this->request->is('post')) {
			//Authenticate this user
			$user = $this->Auth->identify();
			if ($user) {
				$this->Auth->setUser($user);
				return $this->redirect($this->Auth->redirectUrl());
			}
			$this->Flash->error(
				__('Incorrect username or password')
			);
		}
		//set page title
		$title = 'Login | ' . Configure::read('App.Name');
		$this->set(compact('title'));
	}

	/**
	 * Logs Users out and redirects to Home/index
	 * 
	 * @return \Cake\Http\Response|null
	 */
	public function logout()
	{
		return $this->redirect($this->Auth->logout());
	}

	/**
	 * Display Registration page
	 * 
	 * Displays Template/Users/add.ctp
	 * @return \Cake\Http\Response|null
	 */
	public function add()
	{
		$user = $this->Users->newEntity();
		if ($this->request->is('post')) {

			$user = $this->Users->patchEntity($user, $this->request->getData());
			//set player status = offline
			$user->set('player_status_id', PlayerStatus::Offline);
			if ($this->Users->save($user)) {
				$this->Player->createScore($user->get('id'));

				$this->Flash->success(__('The user has been saved.'));

				return $this->redirect(['controller' => 'Users', 'action' => 'login']);
			}
			$this->Flash->error(__('The user could not be saved. Please, try again.'));
		}

		$title = 'Register | ' . Configure::read('App.Name');
		$this->set(compact('title', 'user'));
	}

}
