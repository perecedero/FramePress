<?php echo $this->form(array('controller'=>'test', 'function'=> 'testEmailSend'));?>
	From: <input type="text" name="data[from]" value="here@host.com"><br>
	To: <input type="text" name="data[to]" value="someplace@host2.com"><br>
	Username: <input type="text" name="data[username]" value="Joe"><br>
	Full name: <input type="text" name="data[fullname]" value="Doe"><br><br>
	<input type="submit">
</form>
