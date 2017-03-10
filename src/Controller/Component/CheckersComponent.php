<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Checkers component
 */
class CheckersComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
	protected $_defaultConfig = [];

	//TODO
	//Params JSON of Move
	//Returns True if move succeeded
	public function validateMove($move){
		

		return true;
	}
	//TODO
	//Returns raw gamestate from Game Entity
	public function getGameState(){
		
	}
	//TODO
	public function forfeit(){
		$uid = $this->Auth->user('id');
		return true;
	}

	
	//Validate current User id is same as user
	//in param
	public function validatePlayer($other_user_id){
		$uid = $this->Auth->user('id');
		return ($uid == $other_user_id);
	}

	//TODO
	//Returns true if game status is ended	
	public function isEnded(){

	}

	//TODO
	//Preconf: game is ended
	//Returns winner if exists
	public function getWinner(){

	}

	//TODO
	//Precond: game is ended
	//Adds scores to winner and loser
	public function addScore(){
	
	
	}


}
