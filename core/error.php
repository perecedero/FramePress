<?php

//define core class
if (!class_exists('FramePressError_001')) {
class FramePressError_001
{

	private function callErrorHandler ($type, $view = null, $fileRelativePath = null, $print_now = true )
	{
		if(!$fileRelativePath) {
			$fileRelativePath = substr( $this->status['controller.file'], strpos($this->status['controller.file'], $this->status['plugin.foldername']));
		}

		$this->errorlog[]= array(
			'level' => $this->mapErrorCode(E_USER_WARNING),
			'message' =>$view. ' - controller name: '. $this->status['controller.name'] . ' class: ' . $this->status['controller.class'] . ' method: ' .  $this->status['controller.method'],
			'file'=>$fileRelativePath,
			'line' => 0
		);

		if($this->config['debug'] ) {

			if(in_array($type , array('action', 'shortcode'))) {

					add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
					add_action('the_content', array($this, 'showErrorLog'));

			} else {

				$this->viewSet('fileRelativePath', $fileRelativePath );
				$this->viewSet('fileName', $this->status['controller.name'] );
				$this->viewSet('fileClassName', $this->status['controller.class'] );
				$this->viewSet('fileFunctionName', $this->status['controller.method'] );

				$this->drawView($view, $print_now);
			}
		}

		@restore_error_handler();
		@ini_set('display_errors', false);
		return false;
	}

	public function errorhandler($level = null, $message=null, $file= null, $line = null, $context=null)
	{
		$e = error_get_last();
		$print_now = false;

		if (!$level && !$e) { //shotdown running and nothing found
			return true;
		} else if (!$level && $e) {
			$print_now = true;
			$level = $e['type']; $message=$e['message']; $file= $e['file']; $line = $e['line'];
		}

		//escape reports with @
		if( 0 == ini_get( "error_reporting" ) || 0 == error_reporting() ){
			return;
		}

		//solo informar error si  $file se encuentra en el path de este plugin
		if(strpos($file, $this->status['plugin.fullpath']) === false){
			return;
		}
		$this->errorlog[]= array('level' => $this->mapErrorCode($level),  'message' =>$message, 'file'=> $file, 'line' => $line);

		if ($print_now) {
			$this->showErrorLog();
		} else {
			add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
			add_action('wp_footer', array($this, 'showErrorLog'));
		}
		return true;
	}

	public function mapErrorCode($code) {
		$error =  null;
		switch ($code) {
			case E_PARSE:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$error = 'Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_COMPILE_WARNING:
			case E_RECOVERABLE_ERROR:
				$error = 'Warning';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_STRICT:
				$error = 'Strict';
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$error = 'Deprecated';
				break;
		}
		return $error;
	}

	public function showErrorLog()
	{
		if(!$this->config['debug'] || !$this->errorlog) {
			return;
		}

		echo '<div style="margin:0 auto; width:960px; padding-top: 50px;">';
		foreach ($this->errorlog as $e ){

			if ($e['level'] == 'Error') { $color = '#FFF3F7' ; }
			elseif ($e['level'] == 'Warning') { $color = '#FFFFF3' ; }
			elseif ($e['level'] == 'Notice') { $color = '#F4F3FF' ; }
			elseif ($e['level'] == 'Strict') { $color = '#F9F9F9' ; }
			elseif ($e['level'] == 'Deprecated') { $color = '#F9F9F9' ; }

			echo '<div style="padding:10px; margin:15px; color:565656; border-left:solid 3px #1E90FF; background-color: '.$color.';">' .
			'<div style="font-size:16px; margin-bottom:10px;">' .$e['message'] . '</div>' .
			'<div style="font-size:14px;"> In <b>' . $e['file'] . '</b></div>' .
			'<div style="font-size:14px;"> On line <b>' . $e['line'] . '</b></div>' .
			'</div>';
		}
		echo '</div>';
		$this->errorlog = array();

	}

	public function showPerformanceLog ()
	{
		$log = $this->sessionRead('performance.log');
		$this->sessionWrite('performance.log', array());
		echo '<script>jQuery("#wpfooter").css("position", "relative")</script>';
		foreach($log as $l){ echo '<div style="margin: 10px 0; font: 16px bold;">'.join(' -- ', $l).'</div>'; }
		echo '<br>';
	}


}//end class

//Export framework className
$GLOBALS["FramePressError"] = 'FramePressError_001';
$FramePress = 'FramePressError_001';
