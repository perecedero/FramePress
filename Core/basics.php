<?php



if(!function_exists('framepressGetInstance')){

/**
 * Returns a new FramePress object
 *
 * Used to create a new FremePress instance. The new object will be initiated with the correct
 * context ( plugin paths, plugin main file)  and configured according to the $configuration array
 *
 * @param array $configuration, list of custom configuration values
 * @return object
 */
	function framepressGetInstance($name, $configuration = array())
	{
		global $FramePress;
		global $FramePressInstances;

		if (isset($FramePressInstances[$name])) {
			return $FramePressInstances[$name];
		} else {
			$configuration = array_merge(array('prefix' => $name), $configuration);
			$FramePressInstances[$name] = new $FramePress($configuration);
			return $FramePressInstances[$name];
		}
	}
}

if (!function_exists('fpgi')) {
	function fpgi($name)
	{
		return framepressGetInstance($name);
	}
}


if (!function_exists('pr')) {
/**
 * print_r() convenience function
 *
 * In terminals this will act the same as using print_r() directly, when not run on cli
 * print_r() will wrap <PRE> tags around the output of given array.
 *
 * @param array $var Variable to print out
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#pr
 */
	function pr($var)
	{
		$template = php_sapi_name() !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
		printf($template, print_r($var, true));
	}
}
