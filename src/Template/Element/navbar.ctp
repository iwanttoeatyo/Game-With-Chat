<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header ">
			<a class="navbar-brand"
				 href="<?= $this->Url->build(['controller' => 'Home', 'action' => 'index']) ?>"><?= $this->viewVars['app_name'] ?></a>
		</div>
		<ul class="nav navbar-nav navbar-right">

			<li>
		  <?php if (is_null($this->request->session()->read('Auth.User.username'))): ?>
						<a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'add']) ?>">
							<span class="glyphicon glyphicon-user"></span> Register</a>
		  <?php else : ?>
						<a> <span>Logged in as <?= $this->request->session()->read('Auth.User.username') ?></span></a>
		  <?php endif; ?>
			</li>

			<?php if (is_null($this->request->session()->read('Auth.User.username'))): ?>
					<li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>">
							<span class="glyphicon glyphicon-log-in"></span> Login</a></li>
			<?php else : ?>
					<li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>">
							<span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
			<?php endif; ?>
		</ul>
	</div>
</nav>
