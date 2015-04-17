<?php

 /**
 * Core class for FramePress
 *
 * DESCRIPTION NEEDED
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link			https://github.com/perecedero/FramePress
 * @package		FramePress
 * @subpackage	Core
 * @license		MIT
 * @author		Ivan Lansky (@perecedero)
 */

//Define core class
if (!class_exists('FramePress_015')) {
class FramePress_015
{

	public $config = array(
		'prefix' => null,
		'use.i18n' => true,
		'debug' => false,
		'slave' => false,
	);

	public $paths = array();

	/**
	 * Constructor.
	 *
	 * @param string $config initial user configuration
	*/
	public function __construct($config = array() )
	{
		global $FramePressLoader;

		//get main file
		$bt =  debug_backtrace();
		$this->file =  $bt[1]['file'];

		//detect instance type
		if (strpos($this->file, WP_PLUGIN_DIR) !== false) {
			$this->type = 'plugin';
		} else {
			$this->type = 'theme';
		}

		//set configurations
		$this->config = array_merge($this->config, $config);

		//populate path. this  change depending on type
		$this->populatePaths();

		//set class loader
		$this->Loader = new $FramePressLoader($this);

		//Register activation and deactivation functions
		if ($this->type == 'plugin') {
			$relatuvefile =  basename(dirname($this->file)) . DS . basename($this->file);
			register_activation_hook($relatuvefile, array($this,'_activation'));
			register_deactivation_hook($relatuvefile, array($this, '_deactivation'));
			register_uninstall_hook($relatuvefile, array($this, '_uninstall'));

			//this actions must be made once WP is fully loaded
			add_action('init', array($this, '_Init'), 100);
		} else {
			$this->_init();
		}

	}

    public function __get($name)
    {
		return $this->$name = $this->Loader->load('Core/Lib', $name);
    }

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Initialize the framework
	 *
	 * Load dictionary, register assets, capture output
	 *
	 * @return void
	*/
	public function _init ()
	{
		//Load lenguaje dictionary
		if ($this->config['use.i18n']) {

			$domain = (!is_bool($this->config['use.i18n']))? $this->config['use.i18n'] : $this->config['prefix'];
			$this->config['use.i18n'] = $domain;

			if ($this->type == 'plugin') {
				$path = basename(dirname($this->file)) . str_replace(dirname($this->file), '', $this->paths['lang']);
				load_plugin_textdomain( $domain, false, $path );
			} else {
				load_theme_textdomain( $domain, $this->paths['lang'] );
			}
		}

		//activate error handling && register error reporting assets
		if (!$this->config['slave']) {
			@set_error_handler(array($this->Error, 'capture'));
			@ini_set('display_errors', false);
			@register_shutdown_function (array($this->Error, 'shutdown'));

			wp_deregister_script('FramePressErrors');
			wp_register_script('FramePressErrors', $this->paths['core.js.url'] . DS . 'error.js',  array('jquery'), time(), false);
			wp_deregister_style('FramePressErrors');
			wp_register_style('FramePressErrors', $this->paths['core.css.url'] . DS . 'error.css');

			add_action('admin_enqueue_scripts', array($this->Error, '_addScripts'));
			add_action('wp_enqueue_scripts', array($this->Error, '_addScripts'));
		}

		//Start the output capture
		@ob_start();
	}


	/**
	 * Call activation function
	 *
	 * @return void
	*/
	public function _activation ()
	{
		do_action($this->config['prefix'] . '_activation' );
	}

	/**
	 * Call deactivation function
	 *
	 * @return void
	*/
	public function _deactivation ()
	{
		$this->Session->deleteAll();
		do_action($this->config['prefix'] . '_deactivation' );
	}

	/**
	 * Call uninstall function
	 *
	 * @return void
	*/
	public function _uninstall ()
	{
		$this->Session->deleteAll();
		do_action($this->config['prefix'] . '_uninstall' );
	}


	public function populatePaths()
	{
		//define base url
		if ($this->type == 'plugin') {
			$baseurl = plugins_url('', $this->file);
		} else {
			$baseurl = get_stylesheet_directory_uri();
		}

		//use master core paths if this is a slave instance
		$fullpath =  dirname($this->file);
		$foldername =  basename(dirname($this->file));
		if ($this->config['slave']) {
			$master = framepressGetInstance('master');
			$corepath = $master->paths['core'];
			$corebaseurl = plugins_url('', $master ->file);
		} else {
			$corepath = $fullpath . DS . 'Core';
			$corebaseurl = $baseurl;
		}

		//set paths
		$this->paths = array (
			'core' => $corepath,
			'core.views' => $corepath . DS . 'Views',

			'core.img.url' => $corebaseurl . DS . 'Core' . DS . 'Assets' . DS . 'img',
			'core.css.url' =>  $corebaseurl . DS . 'Core' . DS . 'Assets' . DS . 'css',
			'core.js.url' =>  $corebaseurl . DS . 'Core' . DS . 'Assets' . DS . 'js',

			'controller' => $fullpath . DS . 'Controllers',
			'views' => $fullpath . DS . 'Views',
			'lib' => $fullpath . DS . 'Lib',
			'vendor' => $fullpath . DS . 'Vendors',
			'lang' => $fullpath . DS . 'Languages',

			'assets' => $fullpath . DS . 'Assets',
			'img' => $fullpath . DS . 'Assets' . DS . 'img',
			'css' => $fullpath . DS . 'Assets' . DS . 'css',
			'js' => $fullpath . DS . 'Assets' . DS . 'js',

			'img.url' => $baseurl . DS . 'Assets' . DS . 'img',
			'css.url' => $baseurl . DS . 'Assets' . DS . 'css',
			'js.url' => $baseurl . DS . 'Assets' . DS . 'js',
		);
	}

	/*
	 * Modify  paths
	 * Merge a user path array or add/modify a single path
	 *
	 * @param mixed $key, values to be merged, or a key to add/modify
	 * @param mixed $value
	 * @return void
	*/
	public function path( $key, $value=null)
	{
		//merge configurations
		if (is_array($key)) {
			$this->paths = array_merge($this->paths, $key);
		} else {
			$this->paths[$key] = $value;
		}
	}

	/**
	 * Modify the configuration options
	 * Merge a user configuration array or add/modify a configuration option
	 *
	 * @param mixed $key, values to be merged, or a key to add/modify
	 * @param mixed $value
	 * @return void
	*/
	public function config($key, $value=null)
	{
		if (is_array($key)) {
			$this->config = array_merge($this->config, $key);
		} else {
			$this->config[$key] = $value;
		}
	}

	/**
	 * Perform a redirect using headers
	 *
	 * @param array $url The place for redirect
	 * @return void
	*/
	public function redirect ($url=array())
	{
		@ob_end_clean();

		//$url = $this->router($url);
		wp_redirect($url); exit;
	}




}//end class
}//end if class exists

//Export framework className
$GLOBALS["FramePress"] = 'FramePress_015';
$FramePress = 'FramePress_015';
