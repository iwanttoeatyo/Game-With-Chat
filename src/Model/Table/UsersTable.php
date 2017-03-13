<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PlayerStatuses
 * @property \Cake\ORM\Association\hasOne $LobbyAsPlayer1
 * @property \Cake\ORM\Association\hasOne $LobbyAsPlayer2
 * @property \Cake\ORM\Association\hasOne $Scores
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 */
class UsersTable extends Table
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

		$this->setTable('Users');
		$this->setDisplayField('id');
		$this->setPrimaryKey('id');

		$this->belongsTo('PlayerStatuses', [
			'foreignKey' => 'player_status_id',
			'joinType' => 'INNER'
		]);

		$this->hasOne('LobbyAsPlayer1', [
			'className' => 'Lobbies',
			'foreignKey' => 'player1_user_id'
		]);
		$this->hasOne('LobbyAsPlayer2', [
			'className' => 'Lobbies',
			'foreignKey' => 'player2_user_id'
		]);

		$this->hasOne('Scores', [
			'foreignKey' => 'user_id'
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
			->requirePresence('username', 'create')
			->notEmpty('username')
			->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

		$validator
			->email('email')
			->requirePresence('email', 'create')
			->notEmpty('email')
			->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

		$validator
			->requirePresence('password', 'create')
			->notEmpty('password');

		$validator
			->requirePresence('password_confirm', 'create')
			->notEmpty('password_confirm')
			->add('password_confirm', 'custom', [
				'rule' => function ($value, $context) {
				return $value == $context['data']['password'];
				},
				'message' => 'Passwords must be the same'
			]);

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
		$rules->add($rules->isUnique(['username']));
		$rules->add($rules->isUnique(['email']));
		$rules->add($rules->existsIn(['player_status_id'], 'PlayerStatuses'));

		return $rules;
	}
}
