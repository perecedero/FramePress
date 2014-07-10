<?php

//define core class
if (!class_exists('FramePressSession_001')) {
class FramePressSession_001
{

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

//Export framework className
$GLOBALS["FramePressSession"] = 'FramePressSession_001';
$FramePress = 'FramePressSession_001';
