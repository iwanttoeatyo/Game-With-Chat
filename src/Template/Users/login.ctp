<?php
echo $this->Html->css('base.css');
echo $this->Html->css('cake.css');
$title = 'Login | '.$this->viewVars['app_name'];
$this->assign('title',$title);
?>

<div class="users form">
<?= $this->Flash->render() ?>
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Please enter your username and password') ?></legend>
        <?= $this->Form->control('username') ?>
        <?= $this->Form->control('password', array('type' => 'password'))?>
    </fieldset>
    <?= $this->Form->button(__('Login')); ?>
    <?= $this->Form->end() ?>
</div>
