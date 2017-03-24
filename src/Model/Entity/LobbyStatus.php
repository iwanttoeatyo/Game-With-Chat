<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LobbyStatus Entity
 * Represents a row in Lobby_Statuses Table in Database
 *
 * @property int $id
 * @property string $lobby_status
 *
 * @property \App\Model\Entity\Lobby[] $lobbies
 */
class LobbyStatus extends Entity
{
	const Open = 0;
	const Full = 1;
	const Started = 2;
	const Closed = 3;
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
