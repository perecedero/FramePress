<?php
 /**
 * Path class for FramePress.
 *
 * This class is responsable of known all path for the framework, import customs path from path.php user file,
 * and also create the URLs needed to referenciate resources or controllers
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package       core
 * @subpackage    core.path
 * @since         0.1
 * @license       GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as path_[prefix] (see init.php file), to get unique class names between plugins.
 */

class path_test1 {

	/**
	 * Paths list
	 *
	 * @var array
	 * @access public
	*/
	var $Dir;

	/**
	 * mian file (main.php on root plugin folder) path and name
	 *
	 * @var array
	 * @access public
	*/
	var $main_file;

	/**
	 * local instance of Config Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Config = null;

	/**
	 * Constructor.
	 *
	 * @param string $main_file Path to plugin main file
	 * @access public
	*/
	function __construct( $main_file = null, &$config ) {

		global $FP_SYS_TEMP;

		$this->Config = $config;

		$path_parts = pathinfo($main_file);
		$this->main_file = basename($main_file);

		$this->Dir['P_ROOT'] = $path_parts['dirname'];
		$this->Dir['N_ROOT'] = basename($path_parts['dirname']);

		if(!$FP_SYS_TEMP){
			$FP_SYS_TEMP=$this->Dir['P_ROOT'] . DS . 'tmp';
		}

		$this->Dir['SYSTMP'] = $FP_SYS_TEMP;

		$this->Dir['CORE'] = $this->Dir['P_ROOT'] . DS . '.core';
		$this->Dir['CONFIG'] = $this->Dir['P_ROOT'] . DS . 'config';

		$this->Dir['RESOURCES'] = $this->Dir['P_ROOT'] . DS . 'resources';

		$this->Dir['IMG'] =  $this->Dir['RESOURCES'] . DS . 'img';
		$this->Dir['IMG_URL'] = get_bloginfo( 'wpurl' ).'/wp-content/plugins/'.$this->Dir['N_ROOT'].'/resources/img';

		$this->Dir['CSS'] = $this->Dir['RESOURCES'] . DS . 'css';
		$this->Dir['CSS_URL'] = get_bloginfo( 'wpurl' ).'/wp-content/plugins/'.$this->Dir['N_ROOT'].'/resources/css';

		$this->Dir['JS'] = $this->Dir['RESOURCES'] . DS . 'js';
		$this->Dir['JS_URL'] = get_bloginfo( 'wpurl' ).'/wp-content/plugins/'.$this->Dir['N_ROOT'].'/resources/js';

		$this->Dir['VENDORS'] = $this->Dir['P_ROOT'] . DS . 'vendors';
		$this->Dir['LIB'] = $this->Dir['P_ROOT'] . DS . 'lib';
		$this->Dir['LANG'] = $this->Dir['P_ROOT'] . DS . 'languages';

		$this->Dir['VIEW'] = $this->Dir['P_ROOT'] . DS . 'views';
		$this->Dir['D_VIEW'] = $this->Dir['CORE'] . DS . 'defaults' . DS . 'views';

		$this->Dir['PAGES'] = $this->Dir['P_ROOT'] . DS . 'controllers';
		$this->Dir['ACTIONS'] = $this->Dir['P_ROOT'] . DS . 'controllers';

		//add user defined paths
		if (file_exists($file = $this->Dir['CONFIG'] . DS . 'path.php'))
		{
			require_once ($file);
			if (isset($path)){$this->Dir = array_merge($path, $this->Dir);}
		}
	}


	/**
	 * Create an URL to a controller or resource using a "place" array
	 *
	 * @param array $url place for the href
	 * @return string
	 * @access public
	*/
	function router ($url=array()) {

		//string pased, nothing to do
		if (!is_array($url)){
			return $url;
		}

		//complete $url
		$defaults = array('menu_type' => null, 'controller' => null, 'function'=> null, 'params'=> '');
		$url = array_merge($defaults, $url);

		//correct values
		$url['controller'] = ($url['controller'])?$this->Config->read('prefix').'_'.$url['controller']:$_GET['page'];
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

}

?>
