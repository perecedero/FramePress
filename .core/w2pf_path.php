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

 * IMPORTANT NOTE: class name will be rewrited as w2pf_path_[something] (see w2pf_init.php file), to get unique class names between plugins.
 */

class w2pf_path_test {

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
	var $config=null;

	/**
	 * Constructor.
	 *
	 * @param string $main_file Path to plugin main file
	 * @access public
	*/
	function __construct($main_file=null)
	{
		global $FP_SYS_TEMP;
		global $FP_APP_TEMP;

		$path_parts = pathinfo($main_file);
		$this->main_file = basename($main_file);

		$this->Dir['P_ROOT'] = $path_parts['dirname'];
		$this->Dir['N_ROOT'] = basename($path_parts['dirname']);

		if(!$FP_SYS_TEMP){
			$FP_SYS_TEMP=$this->Dir['P_ROOT'] . DS . 'tmp';
		}

		if(!$FP_APP_TEMP){
			$FP_APP_TEMP=$FP_SYS_TEMP;
		}

		$this->Dir['SYSTMP'] = $FP_SYS_TEMP;
		$this->Dir['WPFTMP'] = $FP_APP_TEMP;

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

		$this->Dir['VIEW'] = $this->Dir['P_ROOT'] . DS . 'views';
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
	 * Set the local instance of Config Class
	 *
	 *
	 * @param string $key Index for value to read
	 * @return mixed
	 * @access public
	*/
	function setconf( &$config ){
		$this->config = $config;
	}

	/**
	 * Create an URL to a controller or resource using a "place" array
	 *
	 * @param array $url place for the href
	 * @return string
	 * @access public
	*/
	function router ($url=array()) {

		if (!is_array($url)){
			return $url;
		}

		//pass the parameter to the funcion
		$params='';
		foreach($url as $key =>$value)
		{
			if(preg_match("/^[[:digit:]]+$/", $key))
			{
				$params.='&amp;fargs[]='.urlencode($value);
			}
		}

		$aux_url = get_bloginfo('wpurl');
		if(isset($url['menu_type'])){
			switch ($url['menu_type']){
				case 'menu': $href= $aux_url.'/wp-admin/admin.php?'; break;
				case 'dashboard': $href= $aux_url.'/wp-admin/index.php?'; break;
				case 'posts': $href= $aux_url.'/wp-admin/edit.php?'; break;
				case 'media': $href= $aux_url.'/wp-admin/upload.php?'; break;
				case 'links': $href= $aux_url.'/wp-admin/link-manager.php?'; break;
				case 'pages': $href= $aux_url.'/wp-admin/edit.php?post_type=page&'; break;
				case 'comments': $href= $aux_url.'/wp-admin/edit-comments.php?'; break;
				case 'appearance': $href= $aux_url.'/wp-admin/themes.php?'; break;
				case 'plugins': $href= $aux_url.'/wp-admin/plugins.php?'; break;
				case 'users': $href= $aux_url.'/wp-admin/users.php?'; break;
				case 'tools': $href= $aux_url.'/wp-admin/tools.php?'; break;
				case 'settings': $href= $aux_url.'/wp-admin/options-general.php?'; break;
				default: $href= $_SERVER['PHP_SELF'].'?'; break;
			}
		}
		else
		{
			$href= $_SERVER['PHP_SELF'].'?';
		}

		$href.='page='.$this->config->read('prefix').'_'.$url['controller'];
		if(isset($url['function'])){$href.='&amp;function='.$url['function'];}
		$href.=$params;

		return $href;
	}

}

?>
