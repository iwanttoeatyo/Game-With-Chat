<?php
echo $this->Html->script('socket.js',['async']);
?>
<div class="col-xs-12 top-container no-padding">
	<div class="col-xs-4 fill placeholder no-padding">


		<div class="top-side-division">
			<div class="division-header">
				<span style="float:left">Games/Lobbies</span>
		  <?php if (isset($user_id)): ?>
						<form method="post" accept-charset="utf-8" action="/lobbies/add" style="">
							<button class="btn btn-primary pull-right" type="submit">New Lobby</button>
						</form>
		  <?php endif; ?>
			</div>
			<div class="list-group-container">
				<ul class="list-group lobbies selectable">
			<?= $this->element('lobby_list', array('lobbies' => $lobbies)) ?>
				</ul>
			</div>

		</div>
		<div class="bottom-side-division">
			<div class="division-header">
				<span>Online Players</span>
			</div>
			<div class="list-group-container">
				<ul class="list-group players selectable">
			<?= $this->element('player_list', array('players' => $players)) ?>
				</ul>
			</div>
		</div>

	</div>

	<div class="col-xs-8 fill">
		<div class="home-container">
			<div class="welcome-header">
				<h1>Welcome to URGame</h1>
			</div>
			<p>text </p>
			<div class="info-container">

			</div>
		</div>
	</div>
</div>
<div class="col-xs-12 bottom-container no-padding ">
	<?= $this->element('chat', array('messages' => $messages, $chat_id, $username, $user_id)) ?>
</div>
