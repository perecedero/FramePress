<?php

//Use the DS to separate the directories
if(!defined('DIRECTORY_SEPARATOR')){define('DIRECTORY_SEPARATOR', '/');}
if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}

//Set a global variable for framepress instances if not already defined by another framepress copy
if(!isset($FramePressInstances)) {
	$FramePressInstances = array();
}

//load files
require __DIR__ . DS . 'Core' . DS . 'basics.php';
require __DIR__ . DS . 'Core' . DS . 'Lib' . DS . 'Loader.php';
require __DIR__ . DS . 'Core' . DS . 'FramePress.php';

//create a master instance of framepress
framepressGetInstance('master');
