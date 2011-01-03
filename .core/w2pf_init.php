<?php

/*
	WordPress Plugin Framework, Init v0.2
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/
	//tools
	if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}

	if (!function_exists('w2pf_eh')){
		function w2pf_eh($level, $message, $file, $line, $context) {
			//Handle user errors, warnings, and notices ourself
			if($level === E_USER_ERROR || $level === E_USER_WARNING || $level === E_USER_NOTICE) {
				echo '<div style="font-size:13px;"><b style="color:#565656;">Frame</b><b style="color:#007cd1;">Press</b>: ' .$message . '</div>';
				return(true); //And prevent the PHP error handler from continuing
			}
			return(false); //Otherwise, use PHP's error handler
		}
	}
	set_error_handler('w2pf_eh');

	//load config file
	$config=array();
	require_once(dirname(dirname(__FILE__)) . DS . 'config' . DS . 'config.php');

	//check for errors
	if( substr(sprintf('%o', fileperms(dirname(dirname(__FILE__)) . DS . 'tmp')), -3) !== '777'){
		trigger_error("Cant write on <b>tmp</b> folder, please change it's permissions", E_USER_ERROR);exit;
	}
	if(!$config['prefix']){
		trigger_error("Please change the value of prefix, on <b>config/config.php</b>", E_USER_ERROR);exit;
	}

	restore_error_handler();

	//rename classes
	$directoryHandle = opendir($directory = dirname(__FILE__));
	while ($contents = readdir($directoryHandle)) {
		if(!preg_match("/^w2pf_init.php$/", $contents) && !preg_match("/^(.)*~$/", $contents) && $contents != '.' && $contents != '..' && !is_dir($directory . DS .$contents)) {
			$code = file_get_contents($directory . DS . $contents);
			preg_match('/class ([a-z0-9]*)_([a-z]*)_(?P<version>\w+)/', $code, $m);
			$code = str_replace("{$m[1]}_{$m[2]}_{$m['version']}", "{$m[1]}_{$m[2]}_{$config['prefix']}", $code);
			file_put_contents($directory . DS . $contents, $code);
		}
	}

	//save globals
	$W2PF_version = $config['prefix'];
	$W2PF='w2pf_core_'.$W2PF_version;
	$GLOBALS["W2PF_version"]=$W2PF_version;
	$GLOBALS["W2PF"]=$W2PF;

	if (!class_exists($W2PF)) {require_once('w2pf_core.php');}

?>
