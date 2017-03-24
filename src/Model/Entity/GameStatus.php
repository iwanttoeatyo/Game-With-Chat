<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GameStatus Entity
 * Represents a row in Game_Statuses Table in Database
 *
 * @property int $id
 * @property string $game_status
 *
 * @property \App\Model\Entity\Game[] $games
 */
class GameStatus extends Entity
{
	const Active = 0;
	const Ended = 1;
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
