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

class Shine_Node_ConstantInclude extends Shine_Node_Abstract {
	
	public $template_path = null;
	
	public function __construct($template_path) {
		$this->template_path = $template_path;
	}
	
	public function render($context) {
	    $template = new Shine($this->template_path);
		if (!is_null($this->template_path)) {
			return $template->render($context);
		} else {
			return '';
		}
	}
}