<?php

 /**
 * Core class for FramePress.
 *
 * This class is responsable of create all core class components and implement some genric functions
 * It also implements the flow for activation and deactivation of the plugin
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link			none yet
 * @package			core
 * @subpackage		core.FramePress
 * @license			GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as core_[prefix] (see init.php file), to get unique class names between plugins.
 */

class core_test1 {

	/**
	 * Instance of Path Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Path = null;

	/**
	 * Instance of Msg Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Msg = null;

	/**
	 * Instance of View Class
	 *
	 * @var Object
	 * @access public
	*/
	var $View = null;

	/**
	 * Instance of Html Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Html = null;

	/**
	 * Instance of Page Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Page = null;

	/**
	 * Instance of Action Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Action = null;

	/**
	 * Instance of Session Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Session = null;

	/**
	 * Instance of Config Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Config = null;

	/**
	 * Name of main file, by default main.php
	 *
	 * @var String
	 * @access public
	*/
	var $main_file = null;

	/**
	 * Constructor. It wil create all the objects necesaries for FramePress
	 *
	 * @param string $main_file Name of the main file
	 * @access public
	*/
	function __construct( $main_file = null ) {

		global $FP_CONFIG;

		$this->main_file = $main_file;

		//core modules
		$core_libs = array ('path', 'view', 'msg', 'page', 'action', 'html', 'session', 'config');
		$core_libs_var = array ('Path', 'View', 'Msg', 'Page', 'Action', 'Html', 'Session', 'Config');

		//load core modules classes && create vars
		for ( $i = 0; $i < count($core_libs); $i++ ){
			require_once ( $core_libs[ $i ] . '.php' );
			$$core_libs_var[ $i ] = $core_libs[ $i ] . '_' . $FP_CONFIG['prefix'];
		}

		//create an intance of each core lib
		$this->Config =& new $Config( $FP_CONFIG );

		$this->Path =& new $Path( $main_file, $this->Config );

		$this->Session =& new $Session( $this->Config );

		$this->Html =& new $Html( $this->Path );

		$this->Msg =& new $Msg($this->Path, $this->Config, $this->Html );

		$this->View =& new $View( $this->Path, $this->Config, $this->Html, $this->Msg );

		$this->Action =& new $Action( $this->Path, $this->View, $this->Config );

		$this->Page =& new $Page( $this->Path, $this->View, $this->Config );

		//register activation and deactivation functions
		register_activation_hook(basename(dirname($main_file)) . DS . basename($main_file), array($this,'activation'));
		register_deactivation_hook(basename(dirname($main_file)) . DS . basename($main_file), array($this, 'deactivation'));

		//capture output
		add_action('admin_init', array($this, 'capture_output'));

		//load languages
		add_action('init', array($this, 'load_languages'));
	}

	/**
	 * Start the output capture to can use headers on the plugin
	 *
	 * @return void
	 * @access public
	*/
	function capture_output () {
		ob_start();
	}

	/**
	 * Start the output capture to can use headers on the plugin
	 *
	 * @return void
	 * @access public
	*/
	function load_languages () {
		if( $this->Config->read('use.i18n') ) {
			load_plugin_textdomain($this->Config->read('prefix'), false, $this->Path->Dir['LANG'] );
		}
	}

	/**
	 * Call activation function
	 *
	 * @return void
	 * @access public
	*/
	function activation () {

		require_once ($this->Path->Dir['CONFIG'] . DS . 'activation.php');
		on_activation();
	}

	/**
	 * Call deactivation function
	 *
	 * @return void
	 * @access public
	*/
	function deactivation () {

		delete_option($this->Session->session_name);
		require_once ($this->Path->Dir['CONFIG'] . DS . 'activation.php');
		on_deactivation();
	}

	/**
	 * Perform a import of a file on lib folder
	 *
	 * @param string $name the place for redirect
	 * @return void
	 * @access public
	*/
	function import ($name) {

		require_once ($this->Path->Dir['LIB'] . DS . $name);
	}

	/**
	 * Perform a redirect using headers
	 *
	 * @param array $url The place for redirect
	 * @param boolean $exitCondition to perform an exit after redirect
	 * @return void
	 * @access public
	*/
	function redirect ($url=array(), $exit= true) {

		ob_end_clean();

		$href = html_entity_decode($this->Path->router($url));

		if($exit) {
			header('Location: '.$href);
			exit;
		} else {
			$this->kick_user($href);
		}
	}

	/**
	 * Close the HTTP conection to give back the control, and keep working
	 *
	 * @param string $href the place for redirect
	 * @return void
	 * @access public
	*/
	function kick_user($href = null) {

		// Close user conection and keep working
		@ob_end_clean();
		@ob_start();
		@ignore_user_abort(true);
		header("Status: 302");
		header('Location: '.$href, true, 302);
		header("Content-Length: 0", true);
		header("Connection: close", true);
		echo str_repeat("\r\n", 128); // for IE
		@ob_end_flush();
		@ob_flush();
		@flush();
	}
}


?>
