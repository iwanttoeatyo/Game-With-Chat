<div class="chat-container fill">
	<div class="list-group-container">
		<ul class="list-group messages">
		<?php foreach ($messages as $message): ?>
			<li class="list-group-item message">
				<span class="username"><?=h($message->username)?>: </span>
				<span class="message-body"><?=h($message->message)?></span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="message-box input-group">
		<input type="hidden" id="chat-id" value="<?=$chat_id?>">
		<input type="hidden" id="username" value="<?=$username?>">
		<input type="hidden" id="user-id" value="<?=$user_id?>">
		<input class="form-control" id="input-message" type="text" placeholder="Type a message">
		<span class="input-group-addon chat-box-btn">
           <button type="button" id="send-msg-btn" class="btn btn-primary">Send </button>
    </span>
	</div>
</div>