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

abstract class Shine_Node_Abstract 
{
    abstract public function render($context);

    public function get_nodes_by_type($nodetype) {
        $nodes = array();
        if ($this instanceof $nodetype) {
            $nodes[] = $this;
        }
        if (property_exists($this, 'nodelist')) {
            $nodes = array_merge($nodes, $this->nodelist->get_nodes_by_type($nodetype));
        }
        return $nodes;
    }
	
	public function isFirst()
	{
		return false;
	}
	
	public function getNodes()
	{
		return array($this);
	}
}