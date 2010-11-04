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

class Shine_Node_AutoEscape extends Shine_Node_Abstract{
	
	public $setting = null;
	
	public $nodelist = null;
	
	public function __construct($setting, $nodelist) {
		$this->setting = $setting;
		$this->nodelist = $nodelist;
	}
	
	public function render($context) {
		$output = $this->nodelist->render($context);
		
		if ($this->setting) {
			return htmlspecialchars($output);
		} else {
			return $output;
		}
    }
}