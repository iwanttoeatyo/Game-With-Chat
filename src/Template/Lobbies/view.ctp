<?php
echo $this->Html->script('socket.js');
echo $this->Html->css('checkers.css');

?>
<div class="col-xs-12 top-container no-padding">
	<div class="col-xs-4 fill placeholder no-padding">


		<div class="top-side-division">
			<div class="division-header">
				<span id="player1-name">
		  <?php if (isset($lobby->player1))
		  echo $lobby->player1->username; ?>
				</span>

			</div>

			<div class="stats fill">
				<div class="wrapper">
					<div id="player1">
						<h3>Player 1</h3>
					</div>
				</div>
				<div class="turn" id="player1Turn"></div>
			</div>

		</div>
		<div class="bottom-side-division">
			<div class="division-header">
		  	<span id="player1-name">
		  <?php if (isset($lobby->player2))
		  echo $lobby->player2->username;
	  else
		  echo "Waiting for player to join..." ?>
				</span>
			</div>
			<div class="stats fill">
				<div class="wrapper">
					<div id="player2">
						<h3>Player 2</h3>
					</div>
				</div>
				<div class="turn" id="player2Turn"></div>
			</div>
		</div>

	</div>

	<div class="col-xs-8 fill">
		<div class="home-container">
			<input type="hidden" id="lobby-id" value="<?= $lobby->id ?>">
			<input type="hidden" id="host-id" value="<?= $lobby->player1_user_id ?>">
			<div class="welcome-header">
				<h1><?= h($lobby->name) ?></h1>
			</div>
			<p>
		  <?php if ($user_id == $lobby->player1_user_id): ?>
						You are the Host of this Lobby
		  <?php elseif ($user_id && $user_id == $lobby->player2_user_id): ?>
						You are Player 2 in this Lobby
		  <?php else: ?>
						You are just spectating this Lobby
		  <?php endif; ?>


		  <?php if (isset($is_player)): ?>
			<form method="post" accept-charset="utf-8" action="/lobbies/start/<?= $lobby->id ?>">
				<button id="start-lobby-btn" class="btn btn-primary"
			<?php if ($lobby->lobby_status_id != \App\Model\Entity\LobbyStatus::Full) : ?>
							disabled
			<?php endif; ?>
								type="submit">Start Lobby
				</button>
			</form>
		<?php endif; ?>

		<?php if (isset($is_player)) : ?>
					<form method="post" accept-charset="utf-8" action="/lobbies/leave/<?= $lobby->id ?>">
						<button id="leave-lobby-btn" class="btn btn-primary" type="submit">Leave Lobby
						</button>
					</form>
		<?php endif; ?>
			</p>
		</div>
	</div>
</div>
<div class="col-xs-12 bottom-container no-padding ">
	<?= $this->element('chat', array('messages' => $messages, 'chat_id' => $lobby->chat_id, $username, $user_id)) ?>
</div>
