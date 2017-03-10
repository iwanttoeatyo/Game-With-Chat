<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Home Controller
 *
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
		$this->render('index');
    }


}
