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

class Shine_Node_Inclusion extends Shine_Node_Abstract
{
	
	public $vars_to_resolve = array();
	
	public $func = null;
	
	public $template = null;

    function __construct($vars_to_resolve, $funcName, $template)
    {
    	foreach ($vars_to_resolve as $var) {
    		$this->vars_to_resolve[] = new Shine_Variable($var);
    	}
		
		$this->func = $funcName;
		
		$this->template = $template;
    }

    /**
     * @see Shine_Node_Abstract::render()
     */
    public function render($context)
    {
    	$resolved_vars = array();
		
		foreach ($this->vars_to_resolve as $var) {
			$resolved_vars[] = $var->resolve($context);
		}
		
		$dict = call_user_func_array($this->func, $resolved_vars);
		
		$t = new Shine($this->template);
		
		return $t->render($dict);
    }
}