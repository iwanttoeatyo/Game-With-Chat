<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Games Controller
 *
 * @property \App\Model\Table\GamesTable $Games
 * @property \App\Controller\Component\CheckersComponent $Checkers
 */
class GamesController extends AppController
{

   	public function initialize(){
		parent::initialize();
		$this->loadComponent('Checkers');
	}

   /**
     * View method
     *
     * @param string|null $id Game id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $game = $this->Games->get($id, [
            'contain' => ['Lobbies', 'GameStatuses']
        ]);

        $this->set('game', $game);
        $this->set('_serialize', ['game']);
    }

	//expects game id as id
	//
	public function getGameState(){
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		
		$game_id = $this->request->getParam('id');

		//TODO get game state from game entity
	}

	//expects forfeiter id as player
	public function forfeit(){
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		
		$uid = $this->request->getParam('player');
		
		//TODO forfeit player if they match
		if(	$this->Checkers->validatePlayer($uid)){

		}
	}


}
