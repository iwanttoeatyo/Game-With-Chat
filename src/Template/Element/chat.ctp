<div class="chat-container fill">
	<div class="list-group-container" style="">
		<ul class="list-group">
		<?php foreach ($messages as $message): ?>
			<li class="list-group-item message"><span class="username"><?=h($message->username)?>: </span><?=h($message->message)?></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="message-box input-group">
		<input class="form-control" type="text" placeholder="Type a message">
		<span class="input-group-addon chat-box-btn">
           <button type="submit" class="btn btn-primary">Send </button>
    </span>
	</div>
</div>