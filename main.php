<?php
	/*
	Plugin Name: [ Your Plugin Name ]
	*/

	//init framework
	require_once( 'Core/FramePress.php' );
	global $test;
	$test  =  framePressGet(array(
		'prefix' => 'testprefix',
		'debug' => true,
	));


	$test->WordPress->shortcodes(array(
		array(
			'tag' => 'test_error',
			'controller' => 'Test',
			'function' => 'errorShortcode',
		)
	));

	$test->WordPress->hooks(array(
		array(
			'tag' => 'actionError',
			'controller' => 'Test',
			'function' => 'errorAjax',
			'is_ajax'=> true,
		)
	));

	$test->WordPress->adminPages(array(
		'settings' => array (
			array (
				'page.title' => 'Refresh fetures configurations',
				'menu.title' => 'Refresh',
				'capability' => 'administrator',
				'controller' => 'Test',
				'function' => 'errorAdminPage',
			),
		),
	));


