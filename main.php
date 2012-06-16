<?php
/*
Plugin Name: FramePress test
Plugin URI: http://example.com
Description:  -
Author: -
Version: -
Author URI: http://example.com
*/

	//init framework
	require_once('.core/init.php');
	global $FramePress;

	//Create a global instance of framepress, choose a unique name
	global $sfp;
	$sfp = new $FramePress(__FILE__);

	//Admin pages to add
	$wp_pages = array (
		'menu' => array (
			array (
				'page.title' => 'framepress, easier impossibru',
				'menu.title' => 'framepress',
				'capability' => 'administrator',
				'controller' => 'main',
				'function' => 'index',
				'icon' => 'logo16.png',
			),
		),
	);

/*
	//actions/filters to add
	$wp_actions = array (
		array(
			'tag' => 'init',
			'controller' => 'main',
			'function' => 'init_handler',
		),
		array(
			'tag' => 'customAction',
			'controller' => 'main',
			'function' => 'custom_handler',
		),
	);
*/

	$sfp->Page->add($wp_pages);
	//$sfp->Action->add($wp_actions);


?>
