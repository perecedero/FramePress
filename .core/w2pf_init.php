<?php

 /**
 * Init script for FramePress.
 *
 * This script is responsable of prepare all needed info and perform pre function to strart de framework
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package       core
 * @subpackage    core.init
 * @since         0.1
 * @license       GPL v2 License
 */

	/**
	 * Use the DS to separate the directories in other defines
	*/
	if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}

	/**
	 * Create a custom error handler
	*/
	if (!function_exists('w2pf_eh')){
		function w2pf_eh($level, $message, $file, $line, $context) {
			//Handle user errors, warnings, and notices ourself
			if($level === E_USER_ERROR || $level === E_USER_WARNING || $level === E_USER_NOTICE) {
				echo '<div style="font-size:13px;"><b style="color:#565656;">Frame</b><b style="color:#007cd1;">Press</b>: ' .$message . '</div>';
				return(false); //And prevent the PHP error handler from continuing
			}
			return(false); //Otherwise, use PHP's error handler
		}
	}

	/**
	 * Start with check proccess
	*/
	set_error_handler('w2pf_eh');


		/**
		 * Load User custom configurations. It also has the prefix to rename all core classes
		*/
		$config=array();
		require_once(dirname(dirname(__FILE__)) . DS . 'config' . DS . 'config.php');

		/**
		 * Check if config file has prefix setted
		*/
		if(!$config['prefix']){
			trigger_error("Please change the value of prefix, on <b>config/config.php</b>", E_USER_WARNING);
		}
		else{

			/**
			 * Check if we can use sys tmp folder
			*/
			$temp=''; $app_temp='';
			if ( !function_exists('sys_get_temp_dir'))
			{
				if( $temp=getenv('TMP') ){} elseif( $temp=getenv('TEMP')){} elseif( $temp=getenv('TMPDIR')){}
			}
			elseif(@realpath(sys_get_temp_dir()))
			{
				$temp=realpath(sys_get_temp_dir());
			}

			if(!file_exists($temp . DS . 'press_tmp'))
			{
				if ( @mkdir($temp . DS . 'press_tmp')){
					@chmod($temp . DS . 'press_tmp', 0777);
					$app_temp=$temp . DS . 'press_tmp';
				}
				else
				{
					if( substr(sprintf('%o', fileperms(dirname(dirname(__FILE__)) . DS . 'tmp')), -3) < '777'){
						trigger_error("Can&#39;t write on <b>". dirname(dirname(__FILE__)) . DS . 'tmp'."</b> folder, please change it's permissions to 777", E_USER_WARNING);
					}
				}
			}

			/**
			 * Rename core classes to get unique names for this plugin
			*/
			$directoryHandle = opendir($directory = dirname(__FILE__));
			while ($contents = readdir($directoryHandle)) {
				if(!preg_match("/^w2pf_init.php$/", $contents) && !preg_match("/^(.)*~$/", $contents) && $contents != '.' && $contents != '..' && !is_dir($directory . DS .$contents)) {
					$code = file_get_contents($directory . DS . $contents);
					preg_match('/class ([a-z0-9]*)_([a-z]*)_(?P<version>\w+)/', $code, $m);
					if ("{$m[1]}_{$m[2]}_{$m['version']}" != "{$m[1]}_{$m[2]}_{$config['prefix']}"){
						$code = str_replace("{$m[1]}_{$m[2]}_{$m['version']}", "{$m[1]}_{$m[2]}_{$config['prefix']}", $code);
						file_put_contents($directory . DS . $contents, $code);
					}
				}
			}

			/**
			 * Save some globals to use them to create core class properly
			*/
			$GLOBALS["FramePress"]='w2pf_core_'.$config['prefix'];
			$GLOBALS["FP_CONFIG"]=$config;
			$GLOBALS["FP_SYS_TEMP"]=$temp;
			$GLOBALS["FP_APP_TEMP"]=$app_temp;

			/**
			 * Chek if class with prefix changed already exist (the neme will be not unique)
			*/
			if (class_exists($FramePress)) {
				trigger_error("Sorry prefix: <b>" . $config['prefix'] . "</b> is used by other plugin on this WP installation, please choose a unique one", E_USER_WARNING);
			}

		}

	restore_error_handler();

	require_once('w2pf_core.php');

?>
