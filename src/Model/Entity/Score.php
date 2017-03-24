<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Score Entity
 * Represents a row in Scores Table in Database
 *
 * @property int $id
 * @property int $user_id
 * @property int $win_count
 * @property int $loss_count
 * @property int $draw_count
 *
 * @property \App\Model\Entity\User $user
 */
class Score extends Entity
{

}
