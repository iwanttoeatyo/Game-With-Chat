<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Game Entity
 * Represents a row in Games Table in Database
 *
 * @property int $id
 * @property int $lobby_id
 * @property int $game_status_id
 * @property string $game_state
 * @property int $winner
 * @property \Cake\I18n\Time $modified_date
 *
 * @property \App\Model\Entity\Lobby $lobby
 * @property \App\Model\Entity\GameStatus $game_status
 */
class Game extends Entity
{

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
