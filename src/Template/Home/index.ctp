<?php
$app_name = 'testappname';
$title = 'URGame';
echo $this->Html->script('socket.js');
?>
	<div class="col-xs-12 top-container no-padding">
		<div class="col-xs-4 fill placeholder no-padding">


			<div class="top-side-division">
				<div class="division-header">
					<span>Games/Lobbies</span>
					<button type="button" class="btn btn-primary pull-right">New Lobby</button>
				</div>
				<div class="list-group-container">

					<ul class="list-group lobbies">
			  <?php foreach ($lobbies as $lobby):?>
						<?php $lobby_status = $lobby->lobby_status->lobby_status ?>
						<li lobby-id="<?=h($lobby->id)?>" class="list-group-item"><?=h($lobby->name)?>
							<span class="badge
							<?php if ( $lobby_status == "Open") : ?>
							btn-success
							<?php  elseif ( $lobby_status == "Started") : ?>
							btn-primary
							<?php  else :?>
							btn-danger
							<?php  endif;?>
							"><?=h($lobby_status)?></span>
						</li>
			  <?php endforeach; ?>
					</ul>
				</div>

			</div>
			<div class="bottom-side-division">
				<div class="division-header">
					<span>Online Players</span>
				</div>
				<div class="list-group-container">
					<ul class="list-group players">
						<?php foreach ($players as $player): ?>
				<?php $player_status = $player->player_status->player_status ?>
							<li user-id="<?=h($player->id)?>" class="list-group-item">
									<span><?=h($player->username)?></span>
								<span class="badge
							<?php if ( $player_status == "Global") : ?>
							btn-success
							<?php  elseif ( $player_status == "Lobby") : ?>
							btn-primary
							<?php  else :?>
							btn-danger
							<?php  endif;?>
							">In <?=h($player_status)?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>

		</div>

		<div class="col-xs-8 fill">
			<div class="home-container">
				<div class="welcome-header">
					<h1>Welcome to URGame</h1>
				</div>
				<p>text text text text text text text text text text text text text text text
					text text text text text text text text text text text text text text text
					text text text text text text texttext text text text text text text
					text text text text text text text text text text text text text text text text
					text text text text text text text text text text text text text text text
					text text text text text text text text text text text text text </p>
			</div>
		</div>
	</div>
	<div class="col-xs-12 bottom-container no-padding ">
		<?=$this->element('chat', array('messages' => $messages, $chat_id, $username, $user_id))?>
	</div>
