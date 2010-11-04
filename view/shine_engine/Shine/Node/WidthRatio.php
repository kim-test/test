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

class Shine_Node_WidthRatio extends Shine_Node_Abstract
{
	public $val_expr = null;
	
	public $max_expr = null;
	
	public $max_width = null;
	
    function __construct($val_expr, $max_expr, $max_width)
    {
    	$this->val_expr = $val_expr;
		$this->max_expr = $max_expr;
		$this->max_width = $max_width;
    }
	
    public function render($context)
    {
    	try {
    		$value = $this->val_expr->resolve($context);
			$maxvalue = $this->max_expr->resolve($context);
    	} catch (Shine_Exception_VariableDoesNotExist $e) {
    		return '';
    	}
		
		$ratio = ((float)$value / (float)$maxvalue) * (int)$this->max_width;
		$ratio = round($ratio);
		
		return (string)((int)$ratio);
    }
}

?>