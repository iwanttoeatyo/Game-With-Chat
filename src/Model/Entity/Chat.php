<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Chat Entity
 * Represents a row in Chats Table in Database
 *
 * @property int $id
 *
 * @property \App\Model\Entity\Message[] $messages
 * @property \App\Model\Entity\Lobby[] $lobbies
 */
class Chat extends Entity
{
	Const GLOBAL_CHAT_ID = 1;
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
