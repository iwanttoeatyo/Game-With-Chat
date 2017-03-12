<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Message Entity
 *
 * @property int $id
 * @property int $chat_id
 * @property \Cake\I18n\Time $created_date
 * @property string $message
 * @property string $username
 *
 * @property \App\Model\Entity\Chat $chat
 */
class Message extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
