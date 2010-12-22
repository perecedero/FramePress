<?php

/*
	WordPress Plugin Framework, Init v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/
$W2PF_version = 'v1';
$W2PF='w2pf_core_'.$W2PF_version;
$GLOBALS["W2PF_version"]=$W2PF_version;
$GLOBALS["W2PF"]=$W2PF;

if (!class_exists($W2PF)) {require_once('w2pf_core.php');}

?>
