<?php
	/*
	Plugin Name: Test Plugin
	Plugin URI:
	Description:  -----
	Author:
	Version:
	Author URI:
	*/

	//init framework
	require_once( 'core/FPL.php' );
	global $FramePress;

/**
*	Create your global instance of framepress, and configure it
*	see "Creating and configuring your instance of framepress" documentation
* 	--------------------------------------------------------------------------------------------------------------------
*/
	global $test;
	$test = new $FramePress(__FILE__, array(
		'prefix' => 'testprefix',
	));


/**
*	Modifing / Adding paths
*	see "Adding custom Paths" documentation
* 	--------------------------------------------------------------------------------------------------------------------
*/

/*
	$test->mergePaths(array(
		'superlibs' => $test->path['lib'] . DS . 'super';
		'duperlibs' => $test->path['lib'] . DS . 'super' . DS . 'duper';
	));
*/


/**
*	Examples for admin pages and actios
* 	see "Adding admin pages" documentation
* 	--------------------------------------------------------------------------------------------------------------------
*/


	//Admin pages to add
	$my_pages = array (
		'menu' => array (
			array (
				'page.title' => 'framepress, easier impossibru',
				'menu.title' => 'FP Test',
				'capability' => 'administrator',
				'controller' => 'test',
				'function' => 'testEmail',
				'icon' => 'logo16.png',
			),
		),
	);
	$test->pages($my_pages);



/**
*	Examples of Actions / filters
* 	see "Adding actions/filters handlers" documentation
* 	--------------------------------------------------------------------------------------------------------------------
*/

/*
	//action/filters
	$my_actions = array (
		array(
			'tag' => 'init',
			'controller' => 'test',
			'function' => 'actionA',
		),
	);
	$test->actions($my_actions);
*/

/**
*	Examples of shortcodes
* 	see "Adding shortcodes handlers" documentation
* 	--------------------------------------------------------------------------------------------------------------------
*/

/*
	$my_shortcodes = array (
		array(
			'tag' => 'my_shortcode',   // will handle [my_shortcode]
			'controller' => 'test',
			'function' => 'shortA',
		),

	);
	$test->shortcodes($my_shortcodes);
*/

?>
