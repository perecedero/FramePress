<?php

//define core class
if (!class_exists('FramePressSession_001')) {
class FramePressSession_001
{

	public $session = array(
		'id' => null,
		'name' => null,
		'time' => 14400, // 3600 * 4  // 4 hours
	);

	public function __construct(&$fp)
	{
		if ($this->config['performance.log']) {
			$this->config['use.session'] = true;
		}

		//copied from old core
		if ($this->config['use.session']) {

			//get session ID from cookies
			$id = null;
			if(!isset($_COOKIE)) {$_COOKIE = array();}
			foreach ($_COOKIE as $key => $value) {
				if(preg_match("/^framepress_session_id_(.)*$/", $key)) { $id = md5($value); break; }
				elseif(preg_match("/^wordpress_logged_in_(.)*$/", $key)) { $id = md5($value); break; }
			}
			if (!$id){
				$name = uniqid('framepress_session_id_', true);
				$value = uniqid(base64_encode(time() . rand() ), true);
				setcookie ($name, $value, time()+$this->session['time'], '/', null, false, true );
				$id = md5($value);
			}

			$this->session['id'] = $id;

			//session_name
			$this->session['name'] = 'fpl_session_' . strtolower($this->config['prefix']);

			//create a global session if not exist
			if ( !$session = get_option($this->session['name']) ) {
				$session = array (
					$this->session['id'] => array (
						'time' => strtotime('now'),
						'data' => array(),
					),
					'Global' => array (
						'time' => strtotime('now'),
						'data' => array(),
					),
				);
			}

			//remove old sessions
			foreach ($session as $key => $value ) {
				if ( ( $value['time'] + $this->session['time'] ) < strtotime('now') ) {
					unset( $session[ $key ] );
				}
			}

			//add user session
			if(!isset($session[$this->session['id']])){
				$session[$this->session['id']] = array (
					'time' => strtotime('now'),
					'data' => array(),
				);
			}

			update_option ($this->session['name'],$session);
		}
	}


	public function sessionRead ($key, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session =get_option($this->session['name'] );
		$session[$id]['time'] = strtotime('now');
		update_option ($this->session['name'], $session);
		return ( isset($session[$id]['data'][$key]) )? $session[$id]['data'][$key] : null ;
	}

	public function sessionCheck ($key, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		update_option ($this->session['name'],$session);
		return  isset($session[$id]['data'][$key]);
	}

	public function sessionDelete ($key, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		unset($session[$id]['data'][$key]);
		$ses = $session;
		update_option ($this->session['name'], $ses );
	}

	public function sessionDestroy ()
	{
		$id = $this->session['id'];
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		$session[$id]['data'] = array();
		update_option ($this->session['name'], $session);
		return true;
	}

	public function sessionWrite ($key, $value, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		$session[$id]['data'][$key] = $value;
		update_option ($this->session['name'], $session );
	}



}//end class
}//end if class exists

//Export framework className
$GLOBALS["FramePressSession"] = 'FramePressSession_001';
$FramePress = 'FramePressSession_001';
