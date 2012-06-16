<?php

 /**
 * Init script for FramePress.
 *
 * This script is responsable of prepare all needed info and perform pre function to strart de framework
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link		  none yet
 * @package       core
 * @subpackage    core.init
 * @since         0.1
 * @license       GPL v2 License
 */


	/**
	 * Use the DS to separate the directories
	*/
	if(!defined('DIRECTORY_SEPARATOR')){define('DIRECTORY_SEPARATOR', '/');}
	if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}

	/**
	 * Create a custom error handler
	*/
	if (!function_exists('framePress_1234567_eh')){
		function framePress_1234567_eh($level, $message, $file, $line, $context) {

			//Handle user errors, warnings, and notices ourself
			echo '<div style="padding:10px; margin:5px; width: 600px; color:565656; border:solid 1px #b6b6b6; background-color: #FFFFE0;">';
			echo '<div style="font-size:16px; margin-bottom:10px;">' .$message . '</div>';
			echo '<div style="font-size:14px;"> In <b>' . $file . '</b></div>';
			echo '<div style="font-size:14px;"> On line <b>' . $line . '</b></div>';
			echo '</div>';

			return true; 
		}
	}

	/**
	 * Create a init function
	*/
	if (!function_exists('framePress_1234567_init')){
		function framePress_1234567_init() {

			/**
			 * default values
			*/
			$fp_config = '';
			$fp_tempPath = '';
			$fp_tempPath_env = array('TMP', 'TEMP', 'TMPDIR');
			$fp_coreName = '';


			/**
			 * Load User custom configurations. It also has the prefix to rename all core classes
			 * $fp_config is filled here
			*/
			require_once(dirname(dirname(__FILE__)) . DS . 'config' . DS . 'config.php');

			/**
			 * Check if config file has set prefix
			*/
			if(!isset($fp_config['prefix'])){
				trigger_error("Please change the value of prefix, on <b>config/config.php</b>", E_USER_WARNING);
				return false;
			}

			/**
			 * Check if prefix is unique
			*/
			$fp_coreName = 'core_'.$fp_config['prefix'];
			if (class_exists( $fp_coreName )) {
				trigger_error("Sorry prefix: <b>" . $fp_config['prefix'] . "</b> is used by other plugin on this WP installation, please choose a unique one", E_USER_WARNING);
				return false;
			}

			/**
			 * Rename core classes to get unique names for this plugin
			*/
			$fp_directory = dirname(__FILE__);
			$fp_directoryHandle = opendir( $fp_directory );

			while ($fp_contents = readdir($fp_directoryHandle)) {

				//modify only core files
				if ( preg_match("/^init.php$/", $fp_contents) || preg_match("/^(.)*~$/", $fp_contents) ||  $fp_contents == '.' && $fp_contents == '..' || is_dir($fp_directory . DS .$fp_contents) ){
					continue;
				}

				//searcj for class name in files
				$fp_code = file_get_contents($fp_directory . DS . $fp_contents);
				preg_match('/class ([a-z]*)_(?P<version>\w+)/', $fp_code, $fp_m);

				//replace it if prefix isn't on class name
				$fp_clasname_current = $fp_m[1] . "_" . $fp_m['version'];
				$fp_clasname_correct = $fp_m[1] . "_" . $fp_config['prefix'];

				if ( $fp_clasname_current != $fp_clasname_correct ){
					$fp_code = str_replace($fp_clasname_current, $fp_clasname_correct, $fp_code);
					if(  @file_put_contents($fp_directory . DS . $fp_contents, $fp_code) === false ) {
						trigger_error("We can not rewrite core classes names, check for write permissions", E_USER_WARNING);
						return false;
					}
				}
			}

			if ($fp_config['use.tmp']) {

				/**
				 * Check if we can use sys tmp folder
				*/
				if ( function_exists('sys_get_temp_dir')) {

					$fp_tempPath=realpath(sys_get_temp_dir());

				}else {

					for($i = 0; $i < count($fp_tempPath_env); $i++){
						if( $fp_tempPath = getenv($fp_tempPath_env[$i]) ){ break; }
					}
				}

				/*
					If not, we need to get the perms to write on our tmp folder
					*this can be commented if the plugin don't need to use a temp folder
				*/
				if(!$fp_tempPath && !is_writable( dirname(dirname(__FILE__)) . DS . 'tmp') ){
					trigger_error("Can&#39;t write on <b>". dirname(dirname(__FILE__)) . DS . 'tmp'."</b> folder, please change it's permissions to 777 or deactive tmp on config.php", E_USER_WARNING);
					return false;
				}
			}

			/**
			 * Save some globals to use them to create core class properly
			*/
			$GLOBALS["FramePress"] = $fp_coreName;
			$GLOBALS["FP_CONFIG"] = $fp_config;
			$GLOBALS["FP_SYS_TEMP"] = $fp_tempPath;

			return true;
		}
	}

	/**
	 * Start with check proccess
	*/

	set_error_handler('framePress_1234567_eh');
	framePress_1234567_init();
	restore_error_handler();

	require_once('framePress.php');

?>
