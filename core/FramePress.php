<?php

 /**
 * Core class for FramePress Lite.
 *
 * DESCRIPTION NEEDED
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link			https://github.com/perecedero/FramePress
 * @package		FramePress
 * @subpackage	core
 * @license		GPL v2 License
 * @author		Ivan Lansky (@perecedero)
 */


//Use the DS to separate the directories
if(!defined('DIRECTORY_SEPARATOR')){define('DIRECTORY_SEPARATOR', '/');}
if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}

//Define core class
if (!class_exists('FramePress_010')) {
class FramePress_010
{
    public $modules;

	public $config = array(
		'prefix' => null,
		'use.i18n' => true,
		'performance.log' => false,
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
	 * @param string $main_file Name of the main file
	 * @param string $config user configuration
	*/
	public function __construct($main_file, $config = array() )
	{
		$fullpath = dirname($main_file);
		$foldername = basename(dirname($main_file));

		//set partial paths
		$this->paths = array (
			'plugin' => $fullpath,
			'core' => $fullpath . DS . 'core',
			'core.views' => $fullpath . DS . 'core' . DS . 'views',
			'core.views.layouts' => $fullpath . DS . 'core' . DS . 'views' . DS . 'layouts',
			'controller' => $fullpath . DS . 'controllers',
			'lib' => $fullpath . DS . 'lib',
			'views' => $fullpath . DS . 'views',
			'layouts' => $fullpath . DS . 'views' . DS . 'layouts',
			'elements' => $fullpath . DS . 'views' . DS . 'elements',
			'lang' => $foldername . DS . 'languages',
			'resource' => $fullpath . DS . 'resources',
			'img' => $fullpath . DS . 'resources' . DS . 'img',
			'img.url' => get_bloginfo( 'wpurl' ) . '/wp-content/plugins/' . $foldername . '/resources/img',
			'css' => $fullpath . DS . 'resources' . DS . 'css',
			'css.url' => get_bloginfo( 'wpurl' ) . '/wp-content/plugins/' . $foldername . '/resources/css',
			'js' => $fullpath . DS . 'resources' . DS . 'js',
			'js.url' => get_bloginfo( 'wpurl' ). '/wp-content/plugins/' . $foldername . '/resources/js',
		);

		//set partial status
		$this->status = array_merge($this->status, array(
			'plugin.fullpath' => $fullpath,
			'plugin.foldername' => $foldername,
			'plugin.mainfile' => basename($main_file),
		));

		//Merge configurations
		$this->config = array_merge($this->config, $config);

		//error handling
		@ini_set('display_errors', false);
		@set_error_handler(array($this->Error, 'capture'));
		@register_shutdown_function (array($this->Error, 'capture'));


		//Load languages
		if ($this->config['use.i18n']) {
			add_action('init', array($this, '_loadLanguages'));
		}

		//Register activation and deactivation functions
		register_activation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this,'_activation'));
		register_deactivation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, '_deactivation'));

		//Start capturing output
		add_action('init', array($this, '_captureOutput'));

	}

    public function __get($name)
    {
		return $this->load('Core', $name);
    }

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Start the output capture to can use headers on the plugin
	 *
	 * @return void
	*/
	public function _captureOutput ()
	{
		@ob_start();
	}

	/**
	 * Load lenguaje dictionary
	 *
	 * @return void
	*/
	public function _loadLanguages ()
	{
		$domain = (!is_bool($this->config['use.i18n']))? $this->config['use.i18n'] : $this->config['prefix'];
		load_plugin_textdomain( $domain, false, $this->paths['lang'] );
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
		if($this->session['name']){
			delete_option($this->session['name']);
		}

		do_action($this->config['prefix'] . '_deactivation' );
	}

	/**
	 * Merge default path with user defined one
	 *
	 * @param array $custom_path user defined path to use with the FramePress
	 * @return void
	*/
	public function mergePaths( $custom_path=array() )
	{
		//merge configurations
		$this->paths = array_merge($this->paths, $custom_path);
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Perform import and instansation of a class.
	 * classes can be controller, core or generic libs
	 *
	 * @param string $type type of Lib ( Core|LIB|Controller)
	 * @param string $name the place for redirect
	 * @return void
	*/
	public function load ($type, $name, $args = null)
	{
		$info = $this->fileInfo($type, $name);

		if(!isset($this->modules[$info['type']][$info['name']])){

			//check && require file
			if($this->fileCheck($info['file'])){
				require_once($info['file']);
			} else {
				return false;
			}

			//get class name, instance  and register object
			$className = $this->fileClassName($info['type'], $info['name']);
			$this->modules[$info['type']][$info['name']] = new $className($this, $args);
		}

		return $this->modules[$info['type']][$info['name']];
	}

	private function fileInfo($type, $name)
	{
		$info = array(
			'type' => $type,
			'type_base' => $type,
			'type_path' => '',
			'name' => rtrim($name, '.php'),
		);

		if( ($subtype = strpos($type, '/')) !== false  ){
			$info['type_base'] = substr($type, 0, $subtype );
			$info['type_path'] =  substr($type, $subtype +1) . DS;
		}

		$info['file'] = $this->paths[strtolower($info['type_base'])] . DS . $info['type_path'] . $info['name'] . '.php';

		return $info;
	}

	private function fileCheck($file)
	{
		if(!file_exists($file)) {
			trigger_error('Missing File | FramePress' );
			return false;
		} elseif(!is_readable($file)) {
			trigger_error('Unreadable File | FramePress' );
			return false;
		} else {
			return true;
		}
	}

	private function fileClassName ($type, $name)
	{
		if($type == 'Controller'){
			return $this->config['prefix'] .  ucfirst(basename($name));
		} else {

			/**
			 * for generic Libs and core libs the
			 * real class name is stored in the *global export var
			*/

			//get the name for the global export var
			$globalExportVarName = ucfirst(basename($name));
			if( $type ==  'Core'){
				$globalExportVarName = 'FramePress' . $globalExportVarName;
			}

			// return the content of the global var (the real class name)
			global $$globalExportVarName;
			return $$globalExportVarName;
		}
	}


	/**
	 * Checi if a deterinated class is loaded
	 *
	 * @param string $type type of Lib (Core|LIB|Controller etc)
	 * @param string $name: name of the loaded class
	 * @return void
	*/
	public function isLoaded ($type, $name)
	{
		$info = $this->fileInfo($type, $name);

		return isset($this->modules[$info['type']][$info['name']]);
	}


	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Perform a redirect using headers
	 *
	 * @param array $url The place for redirect
	 * @return void
	*/
	public function redirect ($url=array())
	{
		@ob_end_clean();

		$url = $this->router($url);

		if($this->config['performance.log']){
			$log = $this->sessionRead('performance.log');
			$log[]=array(
				'request' => 'redirect'. ': ' . $url,
				'time' => 'x',
				'memory' => 'x',
			);
			$this->sessionWrite('performance.log', $log);
		}

		$href = html_entity_decode($url);

		header('HTTP/1.1 302 Found', true);
		header('Status: 302 Found', true);
		header('Location: ' . $href);
		exit;
	}

	/**
	 * Create an URL to a controller or resource using a "place" array
	 *
	 * @param array $url place for the href
	 * @return string
	*/
	public function router ($url=array())
	{
		//string pased, nothing to do
		if (!is_array($url)){
			return $url;
		}

		//complete $url
		$defaults = array('menu_type' => null, 'controller' => null, 'function'=> null, 'params'=> '');
		$url = array_merge($defaults, $url);

		//search menu slug
		if($url['controller']){
			$aux_slug = $this->config['prefix'] . '-' . $url['controller'];
			foreach ($this->pages as $type => $pages){
				for($i=0; $i<count($pages); $i++){
					if (strpos($pages[$i]['menu.slug'], $aux_slug) !== false) {
						$url['menu.slug'] = $pages[$i]['menu.slug'];
						if(!$url['menu_type']){ $url['menu_type'] = $type; }
					}
				}
			}
		}

		//correct values
		$url['controller'] = ($url['controller'])? $url['menu.slug'] : $_GET['page'];
		$url['function'] = ($url['function'])?'&amp;function='.$url['function']:'';

		//parameter to the funcion
		foreach($url as $key =>$value) {
			if(preg_match("/^[[:digit:]]+$/", $key)) { $url['params'].='&amp;fargs[]='.urlencode($value); }
		}

		$wpurl = get_bloginfo('wpurl');
		switch ($url['menu_type']){
			case 'menu':		$base = $wpurl.'/wp-admin/admin.php?'; break;
			case 'dashboard':	$base = $wpurl.'/wp-admin/index.php?'; break;
			case 'posts':		$base = $wpurl.'/wp-admin/edit.php?'; break;
			case 'media':		$base = $wpurl.'/wp-admin/upload.php?'; break;
			case 'links':		$base = $wpurl.'/wp-admin/link-manager.php?'; break;
			case 'pages':		$base = $wpurl.'/wp-admin/edit.php?post_type=page&'; break;
			case 'comments':	$base = $wpurl.'/wp-admin/edit-comments.php?'; break;
			case 'appearance':	$base = $wpurl.'/wp-admin/themes.php?'; break;
			case 'plugins':		$base = $wpurl.'/wp-admin/plugins.php?'; break;
			case 'users':		$base = $wpurl.'/wp-admin/users.php?'; break;
			case 'tools':		$base = $wpurl.'/wp-admin/tools.php?'; break;
			case 'settings':	$base = $wpurl.'/wp-admin/options-general.php?'; break;
			default: 			$base = $_SERVER['PHP_SELF'].'?'; break;
		}

		return $base . 'page=' . $url['controller'] . $url['function'] . $url['params'];
	}


}//end class

}//end if class exists

//Export framework className
$GLOBALS["FramePress"] = 'FramePress_010';
$FramePress = 'FramePress_010';

if(!function_exists('framePressGet')){
	function framePressGet($configuration = array())
	{
		global $FramePress;
		return new $FramePress(dirname(dirname(__FILE__)) . DS . 'main.php', $configuration);
	}
}

	//TESTINGGGG

	//~ function get_bloginfo( $pepe=null ) { return 'null';}
	//~ $test  =  framePressGet(array(
		//~ 'prefix' => 'testprefix',
		//~ 'debug' => true,
	//~ ));
	//~ print_r($test->load('Core/Components/Other', 'Elements'));
