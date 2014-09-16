<?php

//define core class
if (!class_exists('FramePress_Response_001')) {
class FramePress_Response_001
{
	public $body = null;

	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function parse($callReturn = null)
	{
		$type = $this->Core->status['request.type'];

		if ($type == 'adminpage' && is_null($this->body)){
			$this->Core->View->render();
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

			//do shortcodes inside this short code
			$this->body = do_shortcode($this->body);

			return $this->body;
		}

		//filters can return things
		if($type == 'metabox' ){
			return $this->body;
		}

		//filters can return things
		if($type == 'hook' ){
			return $this->body;
		}
	}

}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressResponse"] = 'FramePress_Response_001';
$FramePressResponse = 'FramePress_Response_001';
