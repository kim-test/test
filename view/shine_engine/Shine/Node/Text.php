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

class Shine_Node_Text extends Shine_Node_Abstract {
	
    public $s = null;

    public function __construct($s) {
        $this->s = $s;
    }

    public function render($context) {
        return $this->s;
    }
}