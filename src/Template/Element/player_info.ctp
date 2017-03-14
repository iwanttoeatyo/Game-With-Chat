
<div style="display:block">
	<div>Username: <?= $player->username ?></div>
	<div>
		Player Status: In <?= $player->player_status->player_status ?>
	</div>
	<div>
		Score: <?=$player->score->win_count?> - <?=$player->score->loss_count?>
	</div>
	<div>
		Join Date: <?=date("M/d/Y",strtotime($player->created_date))?>
	</div>
</div>
