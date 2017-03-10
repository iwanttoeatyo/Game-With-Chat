<?php
$app_name = 'testappname';
$title = 'URGame';
?>

	<div class="col-xs-12 top-container no-padding">
		<div class="col-xs-4 fill placeholder no-padding">


			<div class="top-side-division">
				<div class="division-header">
					<span>Games/Lobbies</span>
					<button type="button" class="btn btn-primary pull-right">New Lobby</button>
				</div>
				<div class="list-group-container">

					<ul class="list-group">
						<li class="list-group-item">Lobby #5<span class="badge btn-success">Game Started</span></li>
						<li class="list-group-item">Lobby #6<span class="badge btn-danger">Lobby 2/2</span></li>
						<li class="list-group-item">Lobby #7<span class="badge btn-danger">Lobby 2/2</span></li>
						<li class="list-group-item">Lobby #8<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #9<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #10<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #11<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #12<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #13<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #13<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #13<span class="badge btn-primary">Lobby 1/2</span></li>
						<li class="list-group-item">Lobby #13<span class="badge btn-primary">Lobby 1/2</span></li>
					</ul>
				</div>

			</div>
			<div class="bottom-side-division">
				<div class="division-header">
					<span>Online Players</span>
				</div>
				<div class="list-group-container">
					<ul class="list-group">
						<li class="list-group-item">Player #5<span class="badge btn-warning">In Game</span></li>
						<li class="list-group-item">Player #6<span class="badge btn-warning">In Lobby</span></li>
						<li class="list-group-item">Player #9<span class="badge btn-warning">In Lobby</span></li>
						<li class="list-group-item">Player #10<span class="badge">In Global</span></li>
						<li class="list-group-item">Player #13<span class="badge">In Global</span></li>
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
		<?=$this->element('chat')?>
	</div>
