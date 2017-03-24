<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlayerStatus Entity
 * Represents a row in Player_Statuses Table in Database
 *
 * @property int $id
 * @property string $player_status
 *
 * @property \App\Model\Entity\User[] $users
 */
class PlayerStatus extends Entity
{
	const Offline = 0;
	const Global = 1;
	const Lobby = 2;
	const Game = 3;
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
