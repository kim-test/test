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

class Shine_Node_FirstOf extends Shine_Node_Abstract {
	
	public $vars = array();
	
	public function __construct($vars) {
		foreach ($vars as $val) {
			$this->vars[] = new Shine_Variable($val);
		}
	}
	
    public function render($context) {
    	foreach ($this->vars as $val) {
    		try {
    			$value = $val->resolve($context);
    		} catch (Shine_Exception_VariableDoesNotExist $e) {
    			continue;
    		}
			
			if ($value) return $value;
			
			if ($value === 0) return $value;
    	}
		
		return '';
    }
}