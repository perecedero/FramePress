<?php
	/*
	Plugin Name: [ Your Plugin Name ]
	*/

	//init framework
	require_once 'Core/FramePress.php';
	global $test;
	$test  =  framePressGet(array(
		'prefix' => 'testprefix',
		'here' => __FILE__,
		'debug' => true,
	));

	$test->WordPress->shortcodes(array(
		array(
			'tag' => 'my_short_code',
			'controller' => 'Test',
			'function' => 'shortcode',
		)
	));

	$test->WordPress->hooks(array(
		array(
			'tag' => 'init',
			'controller' => 'Test',
			'function' => 'action',
		)
	));

	$test->WordPress->adminPages(array(
		'settings' => array (
			array (
				'page.title' => 'My first menu Page',
				'menu.title' => 'FramePress Test',
				'capability' => 'administrator',
				'controller' => 'Test',
				'function' => 'adminPage',
			),
		),
		'menu' => array (
			array (
				'page.title' => 'My own menu Page',
				'menu.title' => 'FramePress Menu',
				'capability' => 'administrator',
				'controller' => 'Test',
				'function' => 'ownAdminPage',
			),
		),
	));
