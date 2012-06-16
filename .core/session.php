<?php
/**
 * Session class for FramePress.
 *
 * FramePress simulate sessions.
 * This class is responsable of maintain and handle persistent variables
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link			none yet
 * @package			core
 * @subpackage		core.seccion
 * @since			0.1
 * @license			GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as session_[prefix] (see init.php file), to get unique class names between plugins.
 */


class session_test1 {

	/**
	 * local instance of Config Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Config = null;

	/**
	 * User session Id
	 *
	 * @var String
	 * @access public
	*/
	var $id = null;

	/**
	 * WP option name, used to store sessions for this plugin
	 *
	 * @var String
	 * @access public
	*/
	var $session_name = null;


	function __construct( &$config ){

		$this->Config = $config;

		//get a unique id for user logued
		foreach ($_COOKIE as $key => $value) { if(preg_match("/^wordpress_logged_in_(.)*$/", $key)) {$this->id=md5($value); break;} }
		if (!$this->id){ $this->id = 'Global'; }

		//session_name
		$this->session_name = 'framepress_session_' . $this->Config->read('prefix');

		//create a global session if not exist
		if ( !$session = get_option($this->session_name) ) {
			$session = array (
				$this->id => array (
					'time' => strtotime('now'),
					'data' => array(),
				),
			);
		}

		//remove old sessions
		foreach ($session as $key => $value ) {
			if ( ( $value['time'] + $this->Config->read('session.time') ) < strtotime('now') ) {
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

	function read ($key, $global = null)
	{
		$id = $this->id; if ($global) {$id = 'Global';}

		$session =get_option($this->session_name );
		$session[$id]['time'] = strtotime('now');
		update_option ($this->session_name, $session);

		return ( isset($session[$id]['data'][$key]) )? $session[$id]['data'][$key] : null ;
	}

	function check ($key, $global = null)
	{
		$id = $this->id; if ($global) {$id = 'Global';}

		$session = get_option($this->session_name);
		$session[$id]['time'] = strtotime('now');
		update_option ($this->session_name,$session);

		return  isset($session[$id]['data'][$key]);
	}

	function delete ($key, $global = null)
	{
		$id = $this->id; if ($global) {$id = 'Global';}

		$session = get_option($this->session_name);
		$session[$id]['time'] = strtotime('now');
		unset($session[$id]['data'][$key]);
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
}

?>
