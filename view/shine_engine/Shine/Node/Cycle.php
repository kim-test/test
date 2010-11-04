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

class Shine_Node_Cycle extends Shine_Node_Abstract
{
	public $cycle_iter = array();
	
	public $variable_name = null;
	
    function __construct($cyclevars, $variable_name = null)
    {
    	foreach ($cyclevars as $var) {
    		$this->cycle_iter[] = new Shine_Variable($var);
    	}
		
		$this->variable_name = $variable_name;
    }
	
	protected function cycle_next() {
		$tmp = current($this->cycle_iter);
		$not_end = next($this->cycle_iter);
		
		if ($not_end === false) {
			reset($this->cycle_iter);
		}
		
		return $tmp;
	}
	
    public function render($context)
    {
		$var_instance = $this->cycle_next();
		
		$value = $var_instance->resolve($context);
		
		if ($this->variable_name) $context[$this->variable_name] = $value;
			
        return $value;
    }
}