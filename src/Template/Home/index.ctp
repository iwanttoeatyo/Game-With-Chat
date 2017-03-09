<?php
$this->layout = false;
?>

<!doctype html>
<html>
<head>
	<title>Home | URGame</title>
	<?= $this->html->script('jquery-3.1.1.js') ?>
	<?= $this->html->script('bootstrap.js') ?>
	<?= $this->Html->css('bootstrap.css') ?>
	<?= $this->Html->css('bootstrap-theme.css') ?>
	<?= $this->Html->css('layout.css') ?>
</head>

<body>

<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header ">
			<a class="navbar-brand" href="#">URGame</a>
		</div>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="<?= $this->Url->build(['controller' => 'users' ,'action' => 'add']) ?>">
					<span class="glyphicon glyphicon-user"></span>  Register</a></li>
			<li><a href="<?= $this->Url->build(['controller' => 'users' ,'action' => 'add']) ?>">
					<span class="glyphicon glyphicon-log-in"></span>  Login</a></li>
		</ul>
	</div>
</nav>

<div class="container">

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
		<div class="chat-container fill">
			<div class="list-group-container">
				<ul class="list-group">
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message">
						<p><span class="username">Name:</span> asglkjsdg lksdglksdglksdglksdg lksdglksdglks dglksdglksdglk sdglksdglksdglksdglksdgl
							ksdglksdglkdglks dglksdglksd glksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							ksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							ksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							sdg </p>
					</li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message">
						<p><span class="username">Name:</span> asglkjsdg lksdglksdglksdglksdg lksdglksdglks dglksdglksdglk sdglksdglksdglksdglksdgl
							ksdglksdglkdglks dglksdglksd glksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							ksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							ksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							sdg </p>
					</li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message"><span class="username">Name:</span> asglkjsdglksjdglaksjdg sdg </li>
					<li class="list-group-item message">
						<p><span class="username">Name:</span> asglkjsdg lksdglksdglksdglksdg lksdglksdglks dglksdglksdglk sdglksdglksdglksdglksdgl
							ksdglksdglkdglks dglksdglksd glksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							ksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							ksdglksdglkssdglksdglksd glksdglksdglksdglksdgl ksjdglaksjdg
							sdg </p>
					</li>
				</ul>
			</div>
			<div class="message-box input-group">
				<input class="form-control" type="text" placeholder="Type a message">
				<span class="input-group-addon chat-box-btn">
           <button type="submit" class="btn btn-primary">Send </button>
    </span>
			</div>
		</div>
	</div>
</div>


</body>
</html>