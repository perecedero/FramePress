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


class w2pf_session_v1 {

	var $path = null;
	var $config = null;
	var $file = null;

	function __construct($path, $config){

		$id= null;
		foreach ($_COOKIE as $key => $value)
		{
			if(preg_match("/^wordpress_logged_in_(.)*$/", $key)) {$id=md5($value); break;}
		}

		$this->path = &$path;
		$this->config = &$config;
		$this->file = $this->path->Dir['WPFTMP']. $this->path->DS . 'session' . $id;

		//checkeo si el file existe
		if(!file_exists($this->file))
		{
			file_put_contents($this->file, base64_encode(gzcompress('$data=array();')));
		}

		//checkeo el tiempo de creacion del file
		elseif ((filectime($this->file) + ($this->config->read('session.time'))) < strtotime('now'))
		{
			file_put_contents($this->file, base64_encode(gzcompress('$data=array();'))); 
		}

		//borro los posibles files viejos
		$directoryHandle = opendir($directory=$this->path->Dir['WPFTMP']);
		while ($contents = readdir($directoryHandle)) {
			if($contents != '.' && $contents != '..') {
				$filepath = $directory . $this->path->DS . $contents;
				if((filectime($filepath) + ($this->config->read('session.time'))) < strtotime('now')){
					unlink($filepath);
				}
			}
		}

	}

	function read ($key)
	{
		eval(gzuncompress(base64_decode(file_get_contents($this->file))));
		if(isset($data[$key])){
			return $data[$key];
		}
		return null;
	}

	function check ($key)
	{
		eval(gzuncompress(base64_decode(file_get_contents($this->file))));
		return isset($data[$key]);
	}

	function delete ($key)
	{
		eval(gzuncompress(base64_decode(file_get_contents($this->file))));
		if(isset($data[$key])){
			unset ($data[$key]);
			file_put_contents($this->file, base64_encode(gzcompress('$data='. var_export($data, true).';')));
		}
	}

	function destroy ()
	{
		file_put_contents($this->file, base64_encode(gzcompress('$data=array();')));
	}

	function write ($key, $value)
	{
		eval(gzuncompress(base64_decode(file_get_contents($this->file))));
		$data[$key] = $value;
		file_put_contents($this->file, base64_encode(gzcompress('$data='. var_export($data, true).';')));
	}

}

?>
