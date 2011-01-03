<?php

/*
	WordPress Framework, Path Class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_path_test
{
	var $Dir;
	var $DS;
	var $main_file;
	var $config=null;

	function __construct($main_file=null)
	{

		$path_parts = pathinfo($main_file);
		$this->main_file = basename($main_file);

		$this->DS = DIRECTORY_SEPARATOR;

		//temp dir
		if ( !function_exists('sys_get_temp_dir'))
		{
			if( $temp=getenv('TMP') ){} elseif( $temp=getenv('TEMP')){} elseif( $temp=getenv('TMPDIR')){}
		}
		else
		{
			$temp=realpath(sys_get_temp_dir());
		}
		$this->Dir['SYSTMP'] = $temp;

		$this->Dir['P_ROOT'] = $path_parts['dirname'];
		$this->Dir['N_ROOT'] = basename($path_parts['dirname']);

		$this->Dir['CORE'] = $this->Dir['P_ROOT'] . $this->DS . '.core';
		$this->Dir['CONFIG'] = $this->Dir['P_ROOT'] . $this->DS . 'config';
		$this->Dir['WPFTMP'] = $this->Dir['P_ROOT'] . $this->DS . 'tmp';

		$this->Dir['RESOURCES'] = $this->Dir['P_ROOT'] . $this->DS . 'resources';

		$this->Dir['IMG'] =  $this->Dir['RESOURCES'] . $this->DS . 'img';
		$this->Dir['IMG_URL'] = get_bloginfo( 'url' ).'/wp-content/plugins/'.$this->Dir['N_ROOT'].'/resources/img';

		$this->Dir['CSS'] = $this->Dir['RESOURCES'] . $this->DS . 'css';
		$this->Dir['JS'] = $this->Dir['RESOURCES'] . $this->DS . 'js';

		$this->Dir['VENDORS'] = $this->Dir['P_ROOT'] . $this->DS . 'vendors';
		$this->Dir['LIB'] = $this->Dir['P_ROOT'] . $this->DS . 'lib';

		$this->Dir['VIEW'] = $this->Dir['P_ROOT'] . $this->DS . 'views';
		$this->Dir['PAGES'] = $this->Dir['P_ROOT'] . $this->DS . 'controllers';
		$this->Dir['ACTIONS'] = $this->Dir['P_ROOT'] . $this->DS . 'controllers';

		//add user defined paths
		if (file_exists($file = $this->Dir['CONFIG'] . $this->DS . 'path.php'))
		{
			require_once ($file);
			if (isset($path)){$this->Dir = array_merge($path, $this->Dir);}
		}
	}

	function setconf($config){
		$this->config= &$config;
	}

	function router ($url=array()){

		if (!is_array($url)){

			$href=$url;

		}else{

			//pass the parameter to the funcion
			$params='';
			foreach($url as $key =>$value)
			{
				if(preg_match("/^[[:digit:]]+$/", $key))
				{
					$params.='&amp;fargs[]='.urlencode($value);
				}
			}

			$aux_url = str_replace('https://', '', str_replace('http://', '', get_option('siteurl')));
			if($_SERVER['SERVER_PORT'] != 80)
			{
				$aux_url = str_replace($_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'], '', $aux_url);
			}else{
				$aux_url = str_replace($_SERVER['SERVER_NAME'], '', $aux_url);
			}

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
		}

		return $href;
	}

}

?>
