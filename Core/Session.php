<?php

//define core class
if (!class_exists('FramePress_Session_001')) {
class FramePress_Session_001
{
	public $Core = null;

	public $config = array(
		'name' => null,
		'time' => 14400, // 3600 * 4  // 4 hours
	);

	public $session = null;

	public function __construct(&$fp)
	{
		if(!isset($_COOKIE)) {$_COOKIE = array();}

		$this->Core = $fp;

		//find the session cookie
		$id = null;
		foreach ($_COOKIE as $key => $value) {
			if(preg_match("/^framepress_session_id_(.)*$/", $key)) { $id = md5($value); break; }
		}

		//create one if not setted
		if (!$id){
			$name = uniqid('framepress_session_id_', true);
			$value = uniqid(base64_encode(time() . rand() ), true);
			setcookie ($name, $value, time()+$this->config['time'], '/', null, false, true );
			$id = md5($value);
		}

		$this->config['name'] = 'fpl_session_' . strtolower($this->Core->config['prefix']) . '_' . $id;

		//create a session option if not exist
		if ( !$session = get_option($this->config['name']) ) {
			$session =  array (
				'time' => strtotime('now'),
				'data' => array(),
			);
		}

		//create a new session if it is expired
		if ( ( $session['time'] + $this->config['time'] ) < strtotime('now') ) {
			$session =  array (
				'time' => strtotime('now'),
				'data' => array(),
			);
		}

		$this->session = $session;

		add_action('shutdown', array($this, 'saveSession'));
	}


	public function read ($key)
	{
		$this->session['time'] = strtotime('now');
		return ( isset($this->session['data'][$key]) )? $this->session['data'][$key] : null ;
	}

	public function check ($key, $global = null)
	{
		$this->session['time'] = strtotime('now');
		return  isset($this->session['data'][$key]);
	}

	public function delete ($key, $global = null)
	{
		$this->session['time'] = strtotime('now');
		unset($this->session['data'][$key]);
	}

	public function destroy ()
	{
		$this->session['time'] = strtotime('now');
		$this->session['data'] = array();
		return true;
	}

	public function write ($key, $value, $global = null)
	{
		$this->session['time'] = strtotime('now');
		return $this->session['data'][$key] = $value;
	}


	public function saveSession()
	{
		update_option($this->config['name'], $this->session);
	}




}//end class
}//end if class exists

//Export framework className
$GLOBALS["FramePressSession"] = 'FramePress_Session_001';
$FramePress = 'FramePress_Session_001';
