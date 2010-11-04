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

class Shine_Node_List extends ArrayObject {
    
    public $contains_nontext = false;

    public function render($context) {
    	$bits = array();
    	foreach ($this as $node) {
		    if ($node instanceof Shine_Node_Abstract) {
			    $bits[] = $this->render_node($node, $context);
		    } else {
			    $bits[] = $node;
		    }
    	}
    	return implode('', $bits);
    }

    public function get_nodes_by_type($nodetype) {
    	$nodes = array();
    	foreach ($this as $node) {
    		$nodes = array_merge($nodes, $node->get_nodes_by_type($nodetype));
    	}
    	return $nodes;
    }

    public function render_node($node, $context) {
        return $node->render($context);
    }
}