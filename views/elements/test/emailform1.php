	<h2>Send and email</h2>


	<?php //echo $this->form(array('function'=> 'testEmailSend'));?>
		<input type="text" name="data[from]" placeholder="Sender email address" value="<?php echo $user_email; ?>"><br>
		<input type="text" name="data[to]" placeholder="Recipient email address"><br>
		<textarea name="data[body]" placeholder="Body"></textarea><br>
		<input type="submit">
	</form>
