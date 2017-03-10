<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GameStatuses Model
 *
 * @property \Cake\ORM\Association\HasMany $Games
 *
 * @method \App\Model\Entity\GameStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\GameStatus newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GameStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GameStatus|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GameStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GameStatus[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GameStatus findOrCreate($search, callable $callback = null, $options = [])
 */
class GameStatusesTable extends Table
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

        $this->setTable('game_statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Games', [
            'foreignKey' => 'game_status_id'
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
            ->allowEmpty('game_status');

        return $validator;
    }
}
