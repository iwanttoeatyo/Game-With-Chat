<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PlayerStatuses Model
 *
 * @property \Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\PlayerStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\PlayerStatus newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PlayerStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PlayerStatus|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PlayerStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PlayerStatus[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PlayerStatus findOrCreate($search, callable $callback = null, $options = [])
 */
class PlayerStatusesTable extends Table
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

        $this->setTable('Player_Statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Users', [
            'foreignKey' => 'player_status_id'
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
            ->requirePresence('player_status', 'create')
            ->notEmpty('player_status');

        return $validator;
    }
}
