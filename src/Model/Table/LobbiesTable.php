<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Lobbies Model
 *
 * @property \Cake\ORM\Association\BelongsTo $LobbyStatuses
 * @property \Cake\ORM\Association\BelongsTo $Player1
 * @property \Cake\ORM\Association\BelongsTo $Player2
 * @property \Cake\ORM\Association\BelongsTo $Chats
 * @property \Cake\ORM\Association\HasOne $Games
 *
 * @method \App\Model\Entity\Lobby get($primaryKey, $options = [])
 * @method \App\Model\Entity\Lobby newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Lobby[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Lobby|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Lobby patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Lobby[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Lobby findOrCreate($search, callable $callback = null, $options = [])
 */
class LobbiesTable extends Table
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

        $this->setTable('Lobbies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('LobbyStatuses', [
            'foreignKey' => 'lobby_status_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Player1', [
        	'className' => 'Users',
            'foreignKey' => 'player1_user_id',
            'joinType' => 'INNER'
        ]);
		$this->belongsTo('Player2', [
			'className' => 'Users',
			'foreignKey' => 'player2_user_id',
			'joinType' => 'LEFT'
		]);
        $this->belongsTo('Chats', [
            'foreignKey' => 'chat_id',
            'joinType' => 'INNER'
        ]);
        $this->hasOne('Games', [
            'foreignKey' => 'lobby_id'
        ]);

		$this->addBehavior('Timestamp', [
			'events' => [
				'Model.beforeSave' => [
					'created_date' => 'new',
				]
			]
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->dateTime('created_date')
            ->requirePresence('created_date', 'create')
            ->notEmpty('created_date');

        $validator
            ->boolean('is_locked')
            ->requirePresence('is_locked', 'create')
            ->notEmpty('is_locked');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['lobby_status_id'], 'LobbyStatuses'));
        $rules->add($rules->existsIn(['player1_user_id'], 'Player1'));
        $rules->add($rules->existsIn(['player2_user_id'], 'Player2'));
        $rules->add($rules->existsIn(['chat_id'], 'Chats'));

        return $rules;
    }
}
