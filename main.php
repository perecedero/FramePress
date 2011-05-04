<?php
/*
Plugin Name: TEST
Plugin URI: http://example.com
Description:  a description
Author: infinimedia Inc. Ivan Lansky (Perecedero)
Author URI: http://infinimedia.com/
*/

	//init framework
	require_once('.core/w2pf_init.php');
	global $FramePress;
	global $framepress_test;
	$framepress_test = new $FramePress(__FILE__);

	//pages to add
	$wp_pages = array (
		'tools' => array (
			array (
				'page_title' => 'Tools Title 1',
				'menu_title' => 'Tools Mtitle 1',
				'capability' => 'administrator',
				'menu_slug' => 'first',
			),
		),
	);

	$wp_actions = array (
	/*
		array(
			tag' => 'some_tag',
			handler' => 'principal_controller',
			function' => 'afunction_under_controller',
			'is_ajax' => true,
		),
	*/
	);

	$framepress_test->Page->add($wp_pages);
	$framepress_test->Action->add($wp_actions);

?>
