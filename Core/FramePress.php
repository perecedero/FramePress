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
if (!class_exists('FramePress_012')) {
class FramePress_012
{
    public $modules;

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
	 * @param string $main_file Name of the main file
	 * @param string $config user configuration
	*/
	public function __construct($main_file, $config = array() )
	{
		$fullpath = dirname($main_file);
		$foldername = basename(dirname($main_file));
		$blogurl = get_bloginfo( 'wpurl' );

		//set partial paths
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
			'lang' => $foldername . DS . 'Languages',

			'assets' => $fullpath . DS . 'Assets',
			'img' => $fullpath . DS . 'Assets' . DS . 'img',
			'css' => $fullpath . DS . 'Assets' . DS . 'css',
			'js' => $fullpath . DS . 'Assets' . DS . 'js',

			'img.url' => $blogurl . '/wp-content/plugins/' . $foldername . '/Assets/img',
			'css.url' => $blogurl . '/wp-content/plugins/' . $foldername . '/Assets/css',
			'js.url' => $blogurl. '/wp-content/plugins/' . $foldername . '/Assets/js',
		);

		//set partial status
		$this->status = array_merge($this->status, array(
			'plugin.fullpath' => $fullpath,
			'plugin.foldername' => $foldername,
			'plugin.mainfile' => basename($main_file),
		));

		//Merge configurations
		$this->config = array_merge($this->config, $config);

		if($this->config['debug']) {
			@set_error_handler(array($this->Error, 'capture'));
			@ini_set('display_errors', false);
			@register_shutdown_function (array($this->Error, 'shutdown'));
			add_action('admin_enqueue_scripts', array($this, '_addScripts'));
			add_action('wp_enqueue_scripts', array($this, '_addScripts'));
		}

		//Register activation and deactivation functions
		register_activation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this,'_activation'));
		register_deactivation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, '_deactivation'));
		register_uninstall_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, '_uninstall'));

		add_action('init', array($this, '_Init'), 100);
	}

    public function __get($name)
    {
		return $this->$name = $this->load('Core', $name);
    }

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Initialize the framework
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
	 * Initialize the framework
	 *
	 * @return void
	*/
	public function _addScripts ()
	{
		wp_enqueue_script('FramePressErrors');
		wp_enqueue_style('FramePressErrors');
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
	 * Call deactivation function
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
		$t =  $info['type_base'];
		$n =  $info['name'];

		if($t != 'Core') {
			$this->Request->current('loading', $info);
		}


		if(!isset($this->modules[$t][$n])){

			if($this->fileCheck($info)){
				require_once($info['file']);
			} else {
				return false;
			}

			//get class name
			$className = $this->fileClassName($t, $n);
			if(!$className) {
				$this->modules[$t][$n] = new stdClass();
				return false;
			}

			$this->modules[$t][$n] = new $className($this, $args);
		}


		//bad controller is called again from another hook/shortcode/adminpage/etc
		if( $this->modules[$t][$n] instanceof stdClass) {
			$this->fileClassName($t, $n);
			return false;
		}

		if($t != 'Core') {
			$this->Request->current('loading', false);
		}
		return $this->modules[$info['type_base']][$info['name']];
	}

	private function fileInfo($type, $name)
	{
		$info = array(
			'type' => $type,
			'type_base' => $type,
			'type_path' => '',
			'name' => preg_replace('/.php$/s', '', $name),
		);

		if( ($subtype = strpos($type, '/')) !== false  ){
			$info['type_base'] = substr($type, 0, $subtype );
			$info['type_path'] =  substr($type, $subtype +1) . DS;
		}

		$info['file'] = $this->paths[strtolower($info['type_base'])] . DS . $info['type_path'] . $info['name'] . '.php';

		return $info;
	}

	private function fileCheck($info)
	{
		if(!file_exists($info['file'])) {
			$this->Error->set('Missing File');
			return false;
		} elseif(!is_readable($info['file'])) {
			$fp_status = $this->status;
			$this->Error->set('Unreadable File');
			return false;
		} else {
			return true;
		}
	}

	private function fileClassName ($type, $name)
	{
		if($type == 'Controller'){
			return  ucfirst($this->config['prefix']) . ucfirst($name);
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
			$className =  $$globalExportVarName;
		}

		if($type != 'Core'){
			$l = $this->Request->current('loading');
			$l['class_name'] = $className;
			$this->Request->current('loading', $l);
		}


		if (!class_exists($className)){
			$this->Error->set('Missing Class');
			return false;
		}

		return $className;
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
		wp_redirect($url); exit;
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
		if (!is_array($url)){ return $url; }

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
$GLOBALS["FramePress"] = 'FramePress_012';
$FramePress = 'FramePress_012';

if(!function_exists('framePressGet')){
	function framePressGet($configuration = array())
	{
		global $FramePress;

		if(isset($configuration['here'])){
			$file = $configuration['here'];
		}else{
			$file = dirname(dirname(__FILE__)) . DS . 'main.php';
		}

		return new $FramePress($file, $configuration);
	}
}

if (!function_exists('pr')) {
/**
 * print_r() convenience function
 *
 * In terminals this will act the same as using print_r() directly, when not run on cli
 * print_r() will wrap <PRE> tags around the output of given array.
 *
 * @param array $var Variable to print out
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#pr
 */
	function pr($var) {
		$template = php_sapi_name() !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
		printf($template, print_r($var, true));
	}
}
