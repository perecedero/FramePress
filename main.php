<?php
	/*
	Plugin Name: 
	Plugin URI: 
	Description: 
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

/*
	//Admin pages to add
	$wp_pages = array (
		'menu' => array (
			array (
				'page.title' => 'framepress, easier impossibru',
				'menu.title' => 'Peter test',
				'capability' => 'administrator',
				'controller' => 'main',
				'function' => 'index3',
				//'icon' => 'logo16.png',
			),
		),
	);
	$test->pages($wp_pages);
*/


/**
*	Examples of Actions / filters
* 	see "Adding actions/filters" documentation
* 	--------------------------------------------------------------------------------------------------------------------
*/

/*
	//action/filters
	$wp_actions = array (
		array(
			'tag' => 'init',
			'controller' => 'main',
			'function' => 'actionA',
		),
	);
	$test->actions($wp_actions);
*/
?>
