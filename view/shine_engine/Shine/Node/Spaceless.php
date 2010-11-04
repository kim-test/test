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


class Shine_Node_Spaceless extends Shine_Node_Abstract {
	
	public $nodelist = null;
	
	public function __construct($nodelist) {
		$this->nodelist = $nodelist;
	}
	
	public function render($context) {
		$tmp = trim($this->nodelist->render($context));
		return preg_replace("/>\s+\</", "><", $tmp);
	}
}