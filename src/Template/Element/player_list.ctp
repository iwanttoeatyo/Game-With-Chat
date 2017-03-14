<?php foreach ($players as $player): ?>
	<li user-id="<?= h($player->id) ?>" class="list-group-item">
		<span><?= h($player->username) ?></span>
		<span class="badge
			<?php if ($player->player_status_id == \App\Model\Entity\PlayerStatus::Global) : ?>
				btn-success
			<?php elseif ($player->player_status_id == \App\Model\Entity\PlayerStatus::Lobby) : ?>
				btn-primary
			<?php else : ?>
				btn-danger
			<?php endif; ?>
			">In <?= h($player->player_status->player_status) ?></span>
	</li>
<?php endforeach; ?>
