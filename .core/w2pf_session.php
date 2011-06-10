<?php

/*
	WordPress Framework, HTML class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

/*
configuracion para saber cuanto tiempo dura la seccion
quizas un funcion para start session que haga los checkeos
de tiempo etc, o que haga un destroy y un start

*/


class w2pf_session_test {

	var $path = null;
	var $config = null;
	var $id = null;
	var $session_name = null;
	var $writing = null;

	function __construct($path, $config){

		$this->path = &$path;
		$this->config = &$config;

		//get a unique id for user logued
		foreach ($_COOKIE as $key => $value) { if(preg_match("/^wordpress_logged_in_(.)*$/", $key)) {$this->id=md5($value); break;} }
		if (!$this->id){ return false; }

		//session_name
		$this->session_name = 'framepress_session_' . $this->config->read('prefix');

		//create a global session if not exist
		if ( !$session = get_option($this->session_name) ) {
			$session = array (
				(string)$this->id => array (
					'time' => strtotime('now'),
					'data' => array(),
				),
			);
		}

		//remove old sessions
		foreach ($session as $key => $value ) {
			if ( ( $value['time'] + $this->config->read('session.time') ) < strtotime('now') ) {
				unset( $session[ $key ] );
			}
		}

		//add user session
		if(!isset($session[$this->id])){
			$session[$this->id] = array (
				'time' => strtotime('now'),
				'data' => array(),
			);
		}

		update_option ($this->session_name,$session);
	}

	function read ($key)
	{
		$session =get_option($this->session_name );
		$session[$this->id]['time'] = strtotime('now');
		update_option ($this->session_name, $session);

		return ( isset($session[$this->id]['data'][$key]) )? $session[$this->id]['data'][$key] : null ;
	}

	function check ($key)
	{
		$session = get_option($this->session_name);
		$session[$this->id]['time'] = strtotime('now');
		update_option ($this->session_name,$session);

		return  isset($session[$this->id]['data'][$key]);
	}

	function delete ($key)
	{
		$session = get_option($this->session_name);
		$session[$this->id]['time'] = strtotime('now');
		unset($session[$this->id]['data'][$key]);
		update_option ($this->session_name, $session );
		return true;
	}

	function destroy ()
	{
		$session = get_option($this->session_name);
		$session[$this->id]['time'] = strtotime('now');
		$session[$this->id]['data'] = array();
		update_option ($this->session_name, $session);
		return true;
	}

	function write ($key, $value)
	{
		$session = get_option($this->session_name);
		$session[$this->id]['time'] = strtotime('now');
		$session[$this->id]['data'][$key] = $value;
		update_option ($this->session_name, $session );
		return true;
	}

	private function aprint () {
		print_r(get_option($this->session_name) );

	}

}

?>
