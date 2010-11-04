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

class Shine_Node_IfEqual extends Shine_Node_Abstract
{
	public $var1 = null;
	
	public $var2 = null;
	
	public $nodelist_true = null;
	
	public $nodelist_false = null;
	
	public $negate = null;

    function __construct($var1, $var2, $nodelist_true, $nodelist_false, $negate)
    {
        $this->var1 = new Shine_Variable($var1);
		$this->var2 = new Shine_Variable($var2);
		$this->nodelist_true = $nodelist_true;
		$this->nodelist_false = $nodelist_false;
		$this->negate = $negate;
    }

    public function render($context)
    {
        try {
        	$val1 = $this->var1->resolve($context);
        } catch (Shine_Exception_VariableDoesNotExist $e) {
        	$val1 = null;
        }
		
		try {
        	$val2 = $this->var2->resolve($context);
        } catch (Shine_Exception_VariableDoesNotExist $e) {
        	$val2 = null;
        }
		
		if ((!$this->negate && ($val1 == $val2)) || ($this->negate && ($val1 != $val2))) {
			return $this->nodelist_true->render($context);
		}
		
		return $this->nodelist_false->render($context);
    }
}