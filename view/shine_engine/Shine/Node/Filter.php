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


class Shine_Node_Filter extends Shine_Node_Abstract
{
    public $filter_expr = null;
    
    public $nodelist = null;
    
    public function __construct($filter_expr, $nodelist)
    {
        $this->filter_expr = $filter_expr;
        $this->nodelist = $nodelist;
    }
    
    public function render($context)
    {
        $output = $this->nodelist->render($context);
        $context->update(array('var' => $output));
        $filtered = $this->filter_expr->resolve($context);
        $context->pop();
        return $filtered;
    }
}