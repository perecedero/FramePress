<?php

class TestprefixTest
{

	/*
	 * Handler for  FramePress menu link
	 *
	 * Cause this is a page, this function will render
	 * its view automatically before the function finish
	*/
	public function testEmail ()
	{
		global $test;

	}


	/*
	 * Handler for a link on the testEmail view
	 *
	 * Cause this is a page, this function will render
	 * its view automatically before the function finish
	*/
	public function testEmailSend ()
	{
		global $test, $FramePressEmail;

		//import and create the built in mail object
		$test->import('mail.php');
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

	/*
	 * Handler for the action defined in FramePress
	 * main file (main.php)
	 *
	 * Cause this is an action, this function will render
	 * its view only if requested
	*/
	public function actionA ()
	{
		global $test;

		//do some magic
		//like check something
		//or save something in options

		//now you can print some json if this is and ajax handler
		echo '{"msg"=>"done!"}';
		exit;


		//but if this is a heavy json or a XML, you may prefer to have a view for it

		$data = array('super heavy info...');

		$test->viewSet('data', $data);

		//first argument is the file with the view,
			//null means default (views/test/actionA.php)
		//second argument is print. if print is false
			//the renderer view will be returned as string
		$view = $test-drawView(null, false);
		echo $view;
		exit;

		//or you can simply
		$test-drawView(); exit;
	}


	/*
	 * Handler for the shortcode defined in FramePress
	 * main file (main.php)
	 *
	 * Cause this is an shortcode, this function must return
	 * a string as replacement of the shortcode tag
	*/
	public function shortA ()
	{
		global $test;

		//do some magic
		//like get some value of the database
		//and modify it a little


		//now you can return the string
		return '<a href="#jojo">go to jojo</a>';

		//or you can draw a whole view and returit
		//and rememeber that you can pass info to the view
		$test->viewSet('someval', 'jojo');
		return $test->drawView(null, false);
	}
}


?>
