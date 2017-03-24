<?php
$is_player = $user_id == $lobby->player2_user_id || $user_id == $lobby->player1_user_id;
$lobby_is_started = $lobby->lobby_status_id == \App\Model\Entity\LobbyStatus::Started;
$lobby_is_full = $lobby->lobby_status_id == \App\Model\Entity\LobbyStatus::Full;
$lobby_is_open = $lobby->lobby_status_id == \App\Model\Entity\LobbyStatus::Open;
?>
<div class="list-info">
	<div>Lobby Name: <?= $lobby->name ?></div>
	<div>Player 1: <?php if (isset($lobby->player1))
		  echo $lobby->player1->username; ?></div>
	<div>Player 2:
	  <?php if (isset($lobby->player2))
		  echo $lobby->player2->username;
	  else
		  echo "Waiting for Player..."
	  ?>
	</div>
	<div>
		Lobby Status: <?= $lobby->lobby_status->lobby_status ?>
	</div>

	<div class="flex-form">
	  <?php if (isset($user_id) && (!$lobby_is_started || $is_player)): ?>
		<form method="post" accept-charset="utf-8" action="/lobbies/join/<?= $lobby->id ?>">
			<button class="btn btn-primary"
		  <?php if ($lobby_is_full && !$is_player) : ?>
						disabled
		  <?php endif; ?>
							type="submit">
		  <?php if ($is_player): ?>
			  <?php if ($lobby_is_started): ?>
							Rejoin Game
			  <?php else: ?>
							Rejoin Lobby
			  <?php endif; ?>
		  <?php else: ?>
						Join Lobby
		  <?php endif; ?>
			</button>
		</form>
	  <?php endif; ?>
		<form method="get" accept-charset="utf-8" action="/lobbies/view/<?= $lobby->id ?>">
			<button class="btn btn-primary" type="submit">
		  <?php if ($lobby_is_started): ?>
						Watch  Game
		  <?php else: ?>
						Watch Lobby
		  <?php endif; ?>
			</button>
		</form>
	</div>
</div>