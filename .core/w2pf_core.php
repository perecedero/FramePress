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
 * @link				none yet
 * @package       core
 * @subpackage    core.core
 * @since         0.1
 * @license       GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as w2pf_core_[something] (see w2pf_init.php file), to get unique class names between plugins.
 */

class w2pf_core_test {

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
	function __construct($main_file=null) {

		$this->main_file = $main_file;
		global $FP_CONFIG;

		//Require all core libs
		$core_libs = array ('w2pf_path', 'w2pf_view', 'w2pf_msg', 'w2pf_page', 'w2pf_action', 'w2pf_html', 'w2pf_session', 'w2pf_config');
		for ( $i = 0; $i < count($core_libs); $i++ ){
			require_once ( $core_libs[ $i ] . '.php' );
			$$core_libs[ $i ] = $core_libs[ $i ] . '_' . $FP_CONFIG['prefix'];
		}

		//create an intance of each core lib
		$this->Msg =& new $w2pf_msg();
		$this->Path =& new $w2pf_path($main_file);
		$this->Config =& new $w2pf_config($this->Path, $FP_CONFIG);
		$this->Html =& new $w2pf_html($this->Path);
		$this->Session =& new $w2pf_session($this->Path, $this->Config);
		$this->View =& new $w2pf_view($this->Path, $this->Msg, $this->Html);
		$this->Action =& new $w2pf_action($this->Path, $this->View, $this->Config);
		$this->Page =& new $w2pf_page($this->Path, $this->View, $this->Config);
		$this->Path->setconf($this->Config);

		register_activation_hook(basename(dirname($main_file)) . DS . basename($main_file), array($this,'activation'));
		register_deactivation_hook(basename(dirname($main_file)) . DS . basename($main_file), array($this, 'deactivation'));
		add_action('admin_init', array($this, 'capture_output'));
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
		if($exit)
		{
			header('Location: '.$href);
			exit;
		}
		else
		{
			$this->kick_user($href);
		}
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
