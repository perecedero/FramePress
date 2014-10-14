<?php

//define core class
if (!class_exists('FramePress_Response_001')) {
class FramePress_Response_001
{
	public $body = null;

	public $type = null;

	public $code = '200';

	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function parse()
	{
		$type = $this->Core->status['request.type'];

		if ( in_array($type, array('adminpage', 'metabox')) && is_null($this->body)){
			$this->Core->View->render(null, array('print' => true));
			@ob_end_flush();
		}

		elseif($type == 'shortcode' ){

			//fix for wpautop, that add <p> and <br> to the shortcode content
			global $wp_filter;
			foreach($wp_filter['the_content'] as $priority => $value ){
				if(isset($value['wpautop'])){
					unset($wp_filter['the_content'][$priority]['wpautop']);
				}
			}

			if(is_null($this->body)) {
				$this->body = $this->Core->View->render();
			}

			return do_shortcode($this->body);
		}

		//filters can return things
		elseif($type == 'hook' ){
			if($this->type){
				$this->sendResponse();
			} else {
				return $this->body;
			}
		}
	}

	public function parseError($error = null, $viewContext = null)
	{
		$type = (isset($this->Core->status['request.type']))? $this->Core->status['request.type'] : null;
		//$this->Core->View->set('request_type', $type, $viewContext);

		if ( in_array($type, array('adminpage', 'metabox')) ){
			$this->Core->View->render($error, array('print' => true, 'context' =>$viewContext));
			@ob_end_flush();
		}

		elseif($type == 'shortcode' ){

			//fix for wpautop, that add <p> and <br> to the shortcode content
			global $wp_filter;
			foreach($wp_filter['the_content'] as $priority => $value ){
				if(isset($value['wpautop'])){
					unset($wp_filter['the_content'][$priority]['wpautop']);
				}
			}

			$this->Core->View->render($error, array('print' => true, 'context' =>$viewContext));
		}

		//filters can return things
		else {
			if($this->type){
				$this->sendResponse();
			} else {
				return $this->Core->View->render($error, array('context' =>$viewContext));
			}
		}
	}

	public function sendResponse ()
	{
		if(!headers_sent()){
			header('HTTP/1.1 '.$this->code, true);
			header('Status: '.$this->code, true);
			header('Content-type: '.$this->type, true);
		}

		if($this->type == 'application/json' && !is_string($this->body)) {
			echo json_encode($this->body);
		} else {
			echo $this->body;
		}
		exit;
	}

}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressResponse"] = 'FramePress_Response_001';
$FramePressResponse = 'FramePress_Response_001';
