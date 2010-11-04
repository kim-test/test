<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_Node
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */

class Shine_Node_Now extends Shine_Node_Abstract {
	
	public $format_string = null;
	
	public function __construct($format_string) {
		$this->format_string = $format_string;
	}
	
	public function render($context) {
	    if (isset($_SERVER['REQUEST_TIME'])) {
	        return date($this->format_string, $_SERVER['REQUEST_TIME']);
	    } else {
	        return date($this->format_string);
	    }
	}
}