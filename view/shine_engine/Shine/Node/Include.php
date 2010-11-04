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

class Shine_Node_Include extends Shine_Node_Abstract {
	
	public $template_name = null;
	
	public function __construct($template_name) {
		$this->template_name = new Shine_Variable($template_name);
	}
	
	public function render($context) {
		
		$template_name = $this->template_name->resolve($context);
		
		$t = new Shine($template_name);
		return $t->render($context);
	}
}