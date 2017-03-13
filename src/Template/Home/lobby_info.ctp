<div style="display:block">
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
</div>


<form method="post" accept-charset="utf-8" action="/lobbies/join/<?= $lobby->id ?>">
	<button class="btn btn-primary"
	  <?php if ($lobby->lobby_status_id != \App\Model\Entity\LobbyStatus::Open) : ?>
				disabled
	  <?php endif; ?>
					type="submit">Join Lobby
	</button>
</form>

<form method="get" accept-charset="utf-8" action="/lobbies/view/<?= $lobby->id ?>" >
	<button class="btn btn-primary" type="submit">Watch Lobby</button>
</form>