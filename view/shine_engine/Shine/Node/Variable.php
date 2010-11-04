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

class Shine_Node_Variable extends Shine_Node_Abstract {
    
    public $filter_expression = null;

    public function __construct($filter_expression) {
        $this->filter_expression = $filter_expression;
    }

    public function render($context) {
        $output = $this->filter_expression->resolve($context);
        return $output;
    }
}