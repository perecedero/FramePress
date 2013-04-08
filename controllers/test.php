<?php

class TestprefixTest
{
	public function testEmail ()
	{
		global $test;
	}

	public function testEmailSend ()
	{
		global $test;

		//import and create the mail object
		require_once ( $test->path['d_lib'] . DS . 'mail.php' );
		$mail = new $FramePressEmail($test);

		//configure it
		$mail->config(array(
			'from' => $_POST['data']['from'],
			'to' => $_POST['data']['to'],
			'subject' => 'FramePressEmail test!',
			'template' => 'superEmail',
		));

		//set some view vars && send it
		$mail->viewSet('username', $_POST['data']['username']);
		$mail->viewSet('fullname', $_POST['data']['fullname']);
		$mail->send();

		$test->redirect(array('function'=>'testEmail'));
	}
	
}


?>
