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

class Shine_Node_With extends Shine_Node_Abstract
{
	public $var = null;
	
	public $name = null;
	
	public $nodelist = null;
	
    function __construct($var, $name, $nodelist)
    {
    	$this->var = $var;
		$this->name = $name;
		$this->nodelist = $nodelist;
    }
	
    public function render($context)
    {
    	$val = $this->var->resolve($context);
		$context->push();
		$context[$this->name] = $val;
		$output = $this->nodelist->render($context);
		$context->pop();
		return $output;
    }
}