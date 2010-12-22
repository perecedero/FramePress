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
				'page_title' => 'AppliedSeo',
				'menu_title' => 'SEO',
				'capability' => 'administrator',
				'menu_slug' => 'principal', //page name
			),
		),
	*/
	);

	$wp_actions = array (
	/*
		array(
			tag' => 'appliedseo_init_process',
			handler' => 'principal',
			function' => 'appliedseo_init_process',
			'is_ajax' => true,
		),
	*/
    );

	$w2pf_test->Page->add($wp_pages);
	$w2pf_test->Action->add($wp_actions);

?>
