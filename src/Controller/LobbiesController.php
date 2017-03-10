<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Lobbies Controller
 *
 * @property \App\Model\Table\LobbiesTable $Lobbies
 * @property \App\Controller\Component\LobbyComponent $Lobby
 */
class LobbiesController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = ['Lobby'];

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['LobbyStatuses', 'Player1', 'Player2', 'Chats']
        ];
        $lobbies = $this->paginate($this->Lobbies);

        $this->set(compact('lobbies'));
        $this->set('_serialize', ['lobbies']);
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
        $lobby = $this->Lobbies->get($id, [
            'contain' => ['LobbyStatuses', 'Player1', 'Player2', 'Chats', 'Games']
        ]);

        $this->set('lobby', $lobby);
        $this->set('_serialize', ['lobby']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $lobby = $this->Lobbies->newEntity();
        if ($this->request->is('post')) {
            $lobby = $this->Lobbies->patchEntity($lobby, $this->request->getData());
            if ($this->Lobbies->save($lobby)) {
                $this->Flash->success(__('The lobby has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The lobby could not be saved. Please, try again.'));
        }
        $lobbyStatuses = $this->Lobbies->LobbyStatuses->find('list', ['limit' => 200]);
        $player1 = $this->Lobbies->Player1->find('list', ['limit' => 200]);
        $player2 = $this->Lobbies->Player2->find('list', ['limit' => 200]);
        $chats = $this->Lobbies->Chats->find('list', ['limit' => 200]);
        $this->set(compact('lobby', 'lobbyStatuses', 'player1', 'player2', 'chats'));
        $this->set('_serialize', ['lobby']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Lobby id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $lobby = $this->Lobbies->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $lobby = $this->Lobbies->patchEntity($lobby, $this->request->getData());
            if ($this->Lobbies->save($lobby)) {
                $this->Flash->success(__('The lobby has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The lobby could not be saved. Please, try again.'));
        }
        $lobbyStatuses = $this->Lobbies->LobbyStatuses->find('list', ['limit' => 200]);
        $player1 = $this->Lobbies->Player1->find('list', ['limit' => 200]);
        $player2 = $this->Lobbies->Player2->find('list', ['limit' => 200]);
        $chats = $this->Lobbies->Chats->find('list', ['limit' => 200]);
        $this->set(compact('lobby', 'lobbyStatuses', 'player1', 'player2', 'chats'));
        $this->set('_serialize', ['lobby']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Lobby id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $lobby = $this->Lobbies->get($id);
        if ($this->Lobbies->delete($lobby)) {
            $this->Flash->success(__('The lobby has been deleted.'));
        } else {
            $this->Flash->error(__('The lobby could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
