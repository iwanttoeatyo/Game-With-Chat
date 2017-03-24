<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Lobby Entity
 * Represents a row in Lobbies Table in Database
 *
 * @property int $id
 * @property string $name
 * @property int $lobby_status_id
 * @property int $player1_user_id
 * @property int $player2_user_id
 * @property int $chat_id
 * @property \Cake\I18n\Time $created_date
 * @property bool $is_locked
 *
 * @property \App\Model\Entity\LobbyStatus $lobby_status
 * @property \App\Model\Entity\User $player1
 * @property \App\Model\Entity\User $player2
 * @property \App\Model\Entity\Chat $chat
 * @property \App\Model\Entity\Game[] $games
 */
class Lobby extends Entity
{
	Const Locked = true;
	Const Unlocked = false;
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
