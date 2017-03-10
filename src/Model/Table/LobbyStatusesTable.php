<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LobbyStatuses Model
 *
 * @property \Cake\ORM\Association\HasMany $Lobbies
 *
 * @method \App\Model\Entity\LobbyStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\LobbyStatus newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LobbyStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LobbyStatus|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LobbyStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LobbyStatus[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LobbyStatus findOrCreate($search, callable $callback = null, $options = [])
 */
class LobbyStatusesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('Lobby_Statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Lobbies', [
            'foreignKey' => 'lobby_status_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('lobby_status');

        return $validator;
    }
}
