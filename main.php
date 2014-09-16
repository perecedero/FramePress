<?php
	/*
	Plugin Name: Test Plugin
	Plugin URI:
	Description:
	Author:
	Version:
	Author URI:
	*/

	//init framework
	require_once( 'core/FramePress.php' );
	global $test;
	$test  =  framePressGet(array(
		'prefix' => 'testprefix',
		'debug' => true,
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
	*	Examples for admin pages
	* 	see "Adding admin pages" documentation
	* 	--------------------------------------------------------------------------------------------------------------------
	*/
	$my_pages = array (
		'menu' => array (
			array (
				'page.title' => 'framepress, easier impossibru',
				'menu.title' => 'FP Test',
				'capability' => 'administrator',
				'controller' => 'test',
				'function' => 'testEmail',
				'icon' => 'dashicons-marker',
			),
		),
	);
	$test->WordPress->adminpages($my_pages);


	/**
	*	Examples of Actions / filters
	* 	see "Adding actions/filters handlers" documentation
	* 	--------------------------------------------------------------------------------------------------------------------
	*/
/*
	$my_actions = array (
		array(
			'tag' => 'init',
			'controller' => 'test',
			'function' => 'actionA',
		),
	);
	$test->actions($my_actions);	if (!function_exists('ppr')){

*/

	/**
	*	Examples of shortcodes
	* 	see "Adding shortcodes handlers" documentation
	* 	--------------------------------------------------------------------------------------------------------------------
	*/
	$my_shortcodes = array (
		array(
			'tag' => 'my_shortcode',   // will handle [my_shortcode]
			'controller' => 'test',
			'function' => 'shortA',
		),

	);
	$test->WordPress->shortcodes($my_shortcodes);


	if (!function_exists('pr')){
		function pr ($data){
			echo '<pre>';
			print_r($data);
			echo '</pre>';
		}
	}
