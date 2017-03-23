<?php
echo $this->Html->script('socket.js');
echo $this->Html->css('checkers.css');
echo $this->Html->script('checkers.js');
?>
<input type="hidden" id="game-id" value="<?= $game->id ?>">
<input type="hidden" id="lobby-id" value="<?= $lobby->id ?>">
<input type="hidden" id="player1-id" value="<?= $lobby->player1_user_id ?>">
<input type="hidden" id="player2-id" value="<?= $lobby->player2_user_id ?>">

<!-- alert box will be displayed when a player wins -->
<div id="dialogoverlay"></div>
<div id="dialogbox">
	<div>
		<div id="dialogboxhead"></div>
		<div id="dialogboxbody"></div>
		<div id="dialogboxfoot"></div>
	</div>
</div>
<!-- end of alert box -->

<div class="col-xs-12 top-container no-padding">
	<div class="col-xs-4 fill placeholder no-padding">


		<div class="top-side-division">
			<div class="division-header">
				<span id="player1-name">
			<?= h($lobby->player1->username) ?>
				</span>
		  <?php if  (isset($is_player1)): ?>
						<form class="inline-block pull-right" method="post" accept-charset="utf-8" action="/Games/forfeit">
							<button id="forfeit-game-btn" class="btn btn-danger pull-right">Forfeit</button>
						</form>
		  <?php endif; ?>
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
		  	<span id="player2-name">
		  <?= h($lobby->player2->username) ?>
									</span>
			<?php if  (isset($is_player2)): ?>
					<form class="inline-block pull-right" method="post" accept-charset="utf-8" action="/Games/forfeit">
							<button id="forfeit-game-btn" class="btn btn-danger pull-right">Forfeit</button>
						</form>
			<?php endif; ?>


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
	<div class="col-xs-8 fill no-padding placeholder checkers-box">
		<div id="board">
			<div class="tiles"></div>
			<div class="pieces">
				<div class="player1pieces"></div>
				<div class="player2pieces"></div>
			</div>
		</div>
	</div>
</div>
<div class="col-xs-12 bottom-container no-padding ">
	<?= $this->element('chat', array('messages' => $messages, 'chat_id' => $lobby->chat_id, $username, $user_id)) ?>
</div>
