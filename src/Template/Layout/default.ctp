<!doctype html>
<html>
<head>
	<?= $this->Html->charset() ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>
	  <?= $this->fetch('title')?>
	</title>

	<?= $this->Html->meta('icon') ?>
	<?= $this->fetch('meta') ?>

	<?= $this->Html->script('jquery-3.1.1.js') ?>
	<?= $this->Html->script('bootstrap.js') ?>
	<?= $this->fetch('script') ?>

	<?= $this->Html->css('bootstrap.css') ?>
	<?= $this->Html->css('bootstrap-theme.css') ?>
	<?= $this->Html->css('layout.css') ?>
	<?= $this->fetch('css') ?>
</head>

<body>

<?= $this->element('navbar')?>

<div class="container clearfix">
	<?= $this->fetch('content') ?>
</div>
</body>
</html>