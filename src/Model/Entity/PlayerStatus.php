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
	/**
	 * 	A player is set offline when they disconnect from the websocket.
	 *  Only offline players are not listed in the lobby list.
	 */
	const Offline = 0;
	/**
	 *	A player is in global when they connect to the websocket on the home page
	 */
	const Global = 1;
	/**
	 * A player is in global when they connect to the websocket in a lobby
	 */
	const Lobby = 2;
	/**
	 * A player is in global when they connect to the websocket in a game
	 */
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
