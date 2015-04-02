<?php

//Use the DS to separate the directories
if(!defined('DIRECTORY_SEPARATOR')){define('DIRECTORY_SEPARATOR', '/');}
if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}

//Set a global variable for framepress instances if not already defined by another framepress copy
if(!isset($FramePressInstances)) {
	$FramePressInstances = array();
}

//set main file requested to differentiate betwenn framepress instalations
$mainfile = __DIR__ . DS . 'main.php';

require __DIR__ . DS . 'Core' . DS . 'basics.php';
require __DIR__ . DS . 'Core' . DS . 'Lib' . DS . 'Loader.php';
require __DIR__ . DS . 'Core' . DS . 'FramePress.php';


