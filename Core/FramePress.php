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
if (!class_exists('FramePress_014')) {
class FramePress_014
{

	public $config = array(
		'prefix' => null,
		'use.i18n' => true,
		'debug' => false,
	);

	public $status = array (
		'plugin.mainfile' => null,
		'plugin.fullpath' => null,
		'plugin.foldername' => null,
	);

	public $paths = array();

	/**
	 * Constructor.
	 *
	 * @param string $config initial user configuration
	*/
	public function __construct($name, $config = array() )
	{
		global $mainfile, $FramePressLoader;

		$fullpath = dirname($mainfile);
		$foldername = basename(dirname($mainfile));
		$blogurl = get_bloginfo( 'wpurl' );

		//set paths
		$this->paths = array (
			'plugin' => $fullpath,

			'core' => $fullpath . DS . 'Core',
			'core.views' => $fullpath . DS . 'Core' . DS . 'Views',
			'core.img.url' => $blogurl . '/wp-content/plugins/' . $foldername . '/Core/Assets/img',
			'core.css.url' => $blogurl . '/wp-content/plugins/' . $foldername . '/Core/Assets/css',
			'core.js.url' => $blogurl. '/wp-content/plugins/' . $foldername . '/Core/Assets/js',

			'controller' => $fullpath . DS . 'Controllers',
			'views' => $fullpath . DS . 'Views',
			'lib' => $fullpath . DS . 'Lib',
			'vendor' => $fullpath . DS . 'Vendors',
			'lang' => $foldername . DS . 'Languages',

			'assets' => $fullpath . DS . 'Assets',
			'img' => $fullpath . DS . 'Assets' . DS . 'img',
			'css' => $fullpath . DS . 'Assets' . DS . 'css',
			'js' => $fullpath . DS . 'Assets' . DS . 'js',

			'img.url' => $blogurl . '/wp-content/plugins/' . $foldername . '/Assets/img',
			'css.url' => $blogurl . '/wp-content/plugins/' . $foldername . '/Assets/css',
			'js.url' => $blogurl. '/wp-content/plugins/' . $foldername . '/Assets/js',
		);

		//set status
		$this->status = array_merge($this->status, array(
			'plugin.fullpath' => $fullpath,
			'plugin.foldername' => $foldername,
			'plugin.mainfile' => basename($mainfile),
		));

		//set configurations
		$this->config = array_merge($this->config, $config);

		//set class loader
		$this->Loader = new $FramePressLoader($this);

		//activate error handling
		if($this->config['debug']) {
			@set_error_handler(array($this->Error, 'capture'));
			@ini_set('display_errors', false);
			@register_shutdown_function (array($this->Error, 'shutdown'));
			add_action('admin_enqueue_scripts', array($this->Error, '_addScripts'));
			add_action('wp_enqueue_scripts', array($this->Error, '_addScripts'));
		}

		//Register activation and deactivation functions
		register_activation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this,'_activation'));
		register_deactivation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, '_deactivation'));
		register_uninstall_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, '_uninstall'));

		//some actions must be made once WP is fully loaded
		add_action('init', array($this, '_Init'), 100);
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
			load_plugin_textdomain( $domain, false, $this->paths['lang'] );
		}

		//register error reporting assets
		if($this->config['debug']) {
			wp_deregister_script( 'FramePressErrors' );
			wp_register_script( 'FramePressErrors', $this->paths['core.js.url'] . DS . 'error.js',  array('jquery'), time(), false);
			wp_deregister_style( 'FramePressErrors' );
			wp_register_style( 'FramePressErrors', $this->paths['core.css.url'] . DS . 'error.css');
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

	/**
	 * Merge default path with user defined ones
	 *
	 * @param array $custom_path user defined path to use with the FramePress
	 * @return void
	*/
	public function mergePaths( $custom_path=array() )
	{
		//merge configurations
		$this->paths = array_merge($this->paths, $custom_path);
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
$GLOBALS["FramePress"] = 'FramePress_014';
$FramePress = 'FramePress_014';
