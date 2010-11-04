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

class Shine_Node_If extends Shine_Node_Abstract {
	
	const and_ = 0;
	
	const or_ = 1;
	
	public $bool_exprs = null;
	
	public $nodelist_false = null;
	
	public $nodelist_true = null;
	
	public $link_type = null;
	
	public function __construct($bool_exprs, $nodelist_true, $nodelist_false, $link_type) {
		$this->bool_exprs = $bool_exprs;
		$this->nodelist_true = $nodelist_true;
		$this->nodelist_false = $nodelist_false;
		$this->link_type = $link_type;
	}
	
	public function get_nodes_by_type($nodetype) {
		$nodes = array();
		
		if ($this instanceof $nodetype) {
			$nodes[] = $this;
		}
		
		$nodes = array_merge($nodes, $this->nodelist_true->get_nodes_by_type($nodetype));
		$nodes = array_merge($nodes, $this->nodelist_false->get_nodes_by_type($nodetype));
		
		return $nodes;
	}

	public function render($context) {
        if ($this->link_type == self::or_) {
        	foreach ($this->bool_exprs as $val) {
        		list($ifnot, $bool_expr) = $val;
        		$value = $bool_expr->resolve($context, True);
        		
        		if (($value && !$ifnot) || ($ifnot && !$val)) {
        			return $this->nodelist_true->render($context);
        		}
        	}
        	return $this->nodelist_false->render($context);
        } else {
        	foreach ($this->bool_exprs as $val) {
        		list($ifnot, $bool_expr) = $val;
        		$value = $bool_expr->resolve($context, True);
        		
        		if (!(($value && !$ifnot) || ($ifnot && !$val))) {
        			return $this->nodelist_false->render($context);
        		}
        	}
        	return $this->nodelist_true->render($context);
        }
    }
	
	public function getNodes()
	{
		$nodes = array();
		
		foreach ($this->nodelist_true as $node) {
			$nodes[] = $node;
		}
		
		foreach ($this->nodelist_false as $node) {
			$nodes[] = $node;
		}
		
		return $nodes;
	}
}