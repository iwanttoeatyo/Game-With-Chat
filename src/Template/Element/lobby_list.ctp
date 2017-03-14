<?php foreach ($lobbies as $lobby): ?>
	<li lobby-id="<?= h($lobby->id) ?>" class="list-group-item ui-widget-content">
		<span><?= h($lobby->name) ?></span>
		<span class="badge
			<?php if ($lobby->lobby_status_id == \App\Model\Entity\LobbyStatus::Open) : ?>
				btn-success
			<?php elseif ($lobby->lobby_status_id == \App\Model\Entity\LobbyStatus::Started) : ?>
				btn-primary
			<?php else : ?>
				btn-danger
			<?php endif; ?>
			"><?= h($lobby->lobby_status->lobby_status) ?></span>
	</li>
<?php endforeach; ?>