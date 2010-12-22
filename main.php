<?php
/*
Plugin Name: TEST
Plugin URI: http://example.com
Description:  a description
Author: infinimedia Inc. Ivan Lansky (Perecedero)
Author URI: http://infinimedia.com/
*/

//SIMPLE IS BETTER


	//init framework
	require_once('.core/w2pf_init.php');
	$w2pf_test = new $W2PF(__FILE__);

	//pages to add
	$wp_pages =array (
	/*
		'tools' =>array(//[menu, dashboard, posts, media, links, pages, comments, appearance, plugins, users, tools, settings]
			array(
				'page_title' => 'Tools Title 1',
				'menu_title' => 'Tools Mtitle 1',
				'capability' => 'administrator',
				'menu_slug' => 'principal_controller',
			),
			array(
				'page_title' => 'Tools Title 2',
				'menu_title' => 'Tools Mtitle 2',
				'capability' => 'administrator',
				'menu_slug' => 'second_controller',
			),
		),
		'dashboard' =>array(//[menu, dashboard, posts, media, links, pages, comments, appearance, plugins, users, tools, settings]
			array(
				'page_title' => 'dashboard Title 1',
				'menu_title' => 'dashboard Mtitle 1',
				'capability' => 'administrator',
				'menu_slug' => 'principal_controller',
			),
			array(
				'page_title' => 'dashboard Title 2',
				'menu_title' => 'dashboard Mtitle 2',
				'capability' => 'administrator',
				'menu_slug' => 'second_controller',
			),
		),
	*/
	);

	$wp_actions = array (
	/*
		array(
			tag' => 'some_tag',
			handler' => 'principal_controller',
			function' => 'afunction_under_controller',
			'is_ajax' => true,
		),
		array(
			tag' => 'some_tag_2',
			handler' => 'therd_ctrler',
			function' => 'index',
			'is_ajax' => false,
		),
	*/
	);

	$w2pf_test->Page->add($wp_pages);
	$w2pf_test->Action->add($wp_actions);

?>
