<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 * Represents a row in User Table in Database
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property \Cake\I18n\Time $created_date
 * @property int $player_status_id
 *
 * @property \App\Model\Entity\PlayerStatus $player_status
 * @property \App\Model\Entity\Lobby $lobby_as_player1
 * @property \App\Model\Entity\Lobby $lobby_as_player2
 * @property \App\Model\Entity\Score $score
 */
class User extends Entity
{

	/**
	 * Stores the password using the default Hasher
	 *
	 * @param string $password
	 * @return bool|string
	 *
	 */
	protected function _setPassword($password)
	{
		if (strlen($password) > 0) {
			return (new DefaultPasswordHasher)->hash($password);
		}
	}

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
        'id' => false,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];
}
