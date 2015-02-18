<?php

//define core class
if (!class_exists('FramePress_Loader_001')) {
class FramePress_Loader_001
{

    public $modules;

    public $objects;

	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	/**
	 * Perform import and instantiation of a class.
	 * Class types can be controller, core or generic libs.
	 * Vendors will be only imported
	 *
	 * @param string $type type of Lib ( Core|LIB|Controller|Vendor)
	 * @param string $name the place for redirect
	 * @return void
	*/
	public function load ($type, $name, $args = null)
	{
		$info = $this->fileInfo($type, $name);
		$t =  $info['type_base'];
		$p =  $info['type_path'];
		$n =  $info['name'];

		if($t != 'Core') {
			$this->Core->Request->current('loading', $info);
		}

		if(!isset($this->modules[$t][$p.$n])){

			if($this->fileCheck($info)){
				require_once($info['file']);
			} else {
				return false;
			}

			$this->modules[$t][$p.$n] = 'imported';

			//Vendors don't follow FramePress standard
			if($t != 'Vendor') {

				//get class name
				$className = $this->fileClassName($t, $n);

				if(!$className) {
					$this->objects[$t][$p.$n] = new stdClass();
					return false;
				}

				$this->objects[$t][$p.$n] = new $className($this->Core, $args);
			}

		}

		//vendors only have to be imported
		if($t == 'Vendor') { return true;}


		//bad controller is called again from another hook/shortcode/adminpage/etc
		if( $this->objects[$t][$p.$n] instanceof stdClass) {
			$this->fileClassName($t, $n);
			return false;
		}

		if($t != 'Core') {
			$this->Core->Request->current('loading', false);
		}


		return $this->objects[$t][$p.$n];
	}

	private function fileInfo($type, $name)
	{
		$info = array(
			'type' => $type,
			'type_base' => $type,
			'type_path' => '',
			'name' => preg_replace('/.php$/s', '', $name),
		);

		if( ($subtype = strpos($type, '/')) !== false  ){
			$info['type_base'] = substr($type, 0, $subtype );
			$info['type_path'] =  substr($type, $subtype +1) . DS;
		}

		$basepath =  $this->Core->paths[strtolower($info['type_base'])];
		$info['file'] = $basepath . DS . $info['type_path'] . $info['name'] . '.php';

		return $info;
	}

	private function fileCheck($info)
	{
		if(!file_exists($info['file'])) {
			$this->Core->Error->set('Missing File');
			return false;
		} elseif(!is_readable($info['file'])) {
			$fp_status = $this->status;
			$this->Core->Error->set('Unreadable File');
			return false;
		} else {
			return true;
		}
	}

	private function fileClassName ($type, $name)
	{
		if($type == 'Controller'){
			return  ucfirst($this->Core->config['prefix']) . ucfirst($name);
		} else {

			/**
			 * for generic Libs and core libs the
			 * real class name is stored in the *global export var
			*/

			//get the name for the global export var
			$globalExportVarName = ucfirst(basename($name));
			if( $type ==  'Core'){
				$globalExportVarName = 'FramePress' . $globalExportVarName;
			}

			// return the content of the global var (the real class name)
			global $$globalExportVarName;
			$className =  $$globalExportVarName;
		}

		if($type != 'Core'){
			$l = $this->Core->Request->current('loading');
			$l['class_name'] = $className;
			$this->Core->Request->current('loading', $l);
		}


		if (!class_exists($className)){
			$this->Core->Error->set('Missing Class');
			return false;
		}

		return $className;
	}


	/**
	 * Check if a given class is loaded
	 *
	 * @param string $type type of Lib (Core|LIB|Controller etc)
	 * @param string $name: name of the loaded class
	 * @return void
	*/
	public function isLoaded ($type, $name)
	{
		$info = $this->fileInfo($type, $name);

		return isset($this->modules[$info['type']][$info['name']]);
	}




}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressLoader"] = 'FramePress_Loader_001';
$FramePressLoader = 'FramePress_Loader_001';
