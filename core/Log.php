<?php

//define core class
if (!class_exists('FramePress_Log_001')) {
class FramePress_Log_001
{
	private $performance = null;

	public $Core = null;
	public $Session = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;

		//moved from old core
		if ($this->config['performance.log']){
			if(!$this->sessionCheck('performance.log')) {
				$this->sessionWrite('performance.log', array());
			}
			add_action('in_admin_footer', array($this, 'showPerformanceLog'));
			add_action('wp_footer', array($this, 'showPerformanceLog'));
		}

	}

	public function performance ($action)
	{
		if($action == 'start'){
			$this->performance= array(
				'time' => microtime(true),
				'mem' => memory_get_peak_usage(true),
			);
		} else {

			$endtime = microtime(true);
			$memB = memory_get_peak_usage(true);

			$log = $this->Core->Session->read('performance.log');
			$log[]=array(
				'request' => $this->Core->status['request'],
				'time' => round($endtime - $this->performance['time'], 4). ' s',
				'memory' => (($memB - $this->performance['mem']) / 1024) . ' Kb'
			);
			$this->Core->Session->write('performance.log', $log);
		}
	}


}//end class
}//end if class exists

//Export framework className
$GLOBALS["FramePressLog"] = 'FramePress_Log_001';
$FramePressLog = 'FramePress_Log_001';
