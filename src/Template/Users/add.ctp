<?php
echo $this->Html->css('base.css');
echo $this->Html->css('cake.css');

?>

<div class="form columns content">
	<?= $this->Form->create($user) ?>
	<fieldset>
		<legend><?= __('Register') ?></legend>
	  <?php
	  echo $this->Form->control('username', array('type' => 'text'));
	  echo $this->Form->control('email');
	  echo $this->Form->control('password', array('type' => 'password'));
	  echo $this->Form->control('password_confirm', array('type' => 'password'));
	  ?>
	</fieldset>
	<?= $this->Form->button(__('Submit')) ?>
	<?= $this->Form->end() ?>
</div>
