<?php
/**
 * Config class for FramePress.
 *
 * FramePress permit create user custom configuratios
 * This class is responsable of manage the configutatio list
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package       core
 * @subpackage    core.config
 * @since         0.1
 * @license       GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as config_[prefix] (see init.php file), to get unique class names between plugins.
 */

class config_test1 {

	/**
	 * Configuration list
	 *
	 * @var array
	 * @access public
	 */
	var $custom_config = null;

	/**
	 * Constructor.
	 *
	 * @param object $path Reference to Path class instance created on Core class
	 * @access public
	 */
	function __construct( $config ) {

		$this->custom_config = $config;
	}

	/**
	 * Read a configuration value from user custom configuration file
	 * Return value on success or false on fail
	 *
	 * @param string $key Index for value to read
	 * @return mixed
	 * @access public
	 */
	function read ( $key ) {

		if(isset($this->custom_config[$key])){
			return $this->custom_config[$key];
		}
		return false;
	}
}

?>
