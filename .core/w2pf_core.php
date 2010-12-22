<?php

/*
	WordPress Plugin Framework, Core class v1.2
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_core_v1 {

	var $Path;
	var $Msg;
	var $View;
	var $Page;
	var $Action;
	var $Session;
	var $Config;
	
	var $main_file = null;

	function __construct($main_file=null)
	{
		$this->main_file = $main_file;


		global $W2PF_version;


		if (!class_exists($w2pf_path = 'w2pf_path_'.$W2PF_version)) {require_once('w2pf_path.php');}

		if (!class_exists($w2pf_view = 'w2pf_view_'.$W2PF_version)) {require_once('w2pf_view.php');}
		if (!class_exists($w2pf_msg = 'w2pf_msg_'.$W2PF_version)) {require_once('w2pf_msg.php');}
		if (!class_exists($w2pf_page = 'w2pf_page_'.$W2PF_version)) {require_once('w2pf_page.php');}
		if (!class_exists($w2pf_action = 'w2pf_action_'.$W2PF_version)) {require_once('w2pf_action.php');}
		if (!class_exists($w2pf_html = 'w2pf_html_'.$W2PF_version)) {require_once('w2pf_html.php');}
		if (!class_exists($w2pf_session = 'w2pf_session_'.$W2PF_version)) {require_once('w2pf_session.php');}
		if (!class_exists($w2pf_config = 'w2pf_config_'.$W2PF_version)) {require_once('w2pf_config.php');}

		$this->Msg = new $w2pf_msg();
		$this->Path = new $w2pf_path($main_file);
		$this->View = new $w2pf_view(&$this->Path, &$this->Msg, $w2pf_html);
		$this->Config = new $w2pf_config(&$this->Path);
		$this->Page = new $w2pf_page(&$this->Path, &$this->View, &$this->Config);
		$this->Action = new $w2pf_action(&$this->Path, &$this->View);
		$this->Session = new $w2pf_session(&$this->Path, &$this->Config);
		$this->Path->setconf(&$this->Config);

		register_activation_hook(basename(dirname($main_file)) . '/' . basename($main_file), array($this,'activation'));
		register_deactivation_hook(basename(dirname($main_file)) . '/' . basename($main_file), array($this, 'deactivation'));
		add_action('admin_init', array($this, 'capture_output'));
	}

	function capture_output (){
		ob_start();
	}


	function activation (){
		require_once ($this->Path->Dir['CONFIG'] . $this->Path->DS . 'activation.php');
		on_activation();
	}

	function deactivation (){
		require_once ($this->Path->Dir['CONFIG'] . $this->Path->DS . 'activation.php');
		on_deactivation();
	}

	function redirect ($url=array())
	{
		ob_end_clean();
		$href = html_entity_decode($this->Path->router($url));
		header('Location: '.$href);

		//exit("<script>document.location.href='{$href}'</script>");
	}

	function import ($name){
		require_once ($this->Path->Dir['LIB'].'/'. $name);
	}
}


?>
