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

class Shine_Node_Extends extends Shine_Node_Abstract
{
	public $nodelist = null;
	
	public $parent_name = null;
	
	public $parent_name_expr = null;
	
    public function __construct($nodelist, $parent_name, $parent_name_expr)
    {
    	$this->nodelist = $nodelist;
		$this->parent_name = $parent_name;
		$this->parent_name_expr = $parent_name_expr;
    }
	
	public function isFirst()
    {
        return true;
    }
	
	public function get_parent($context)
	{
		if ($this->parent_name_expr) {
			$this->parent_name = $this->parent_name_expr->resolve($context);
		}
		
		$parent = $this->parent_name;
		
		if (!$parent) {
			throw new Shine_Exception('Invalid template name in "extends" tag');
		}
		
        if ($parent instanceof Shine) {
            return $parent;
        }
		
		return new Shine($parent);
	}
	
    public function render($context)
    {
    	$compiled_parent = $this->get_parent($context);
		$parent_blocks = array();
		foreach ($compiled_parent->nodelist->get_nodes_by_type('Shine_Node_Block') as $value) {
			$parent_blocks[$value->name] = $value;
		}
		foreach ($this->nodelist->get_nodes_by_type('Shine_Node_Block') as $block_node) {
			
			if (isset($parent_blocks[$block_node->name])) {
				$parent_block = $parent_blocks[$block_node->name];
				$parent_block->parent = $block_node->parent;
                $parent_block->add_parent($parent_block->nodelist);
                $parent_block->nodelist = $block_node->nodelist;
			} else {
				foreach ($compiled_parent->getAllNodes() as $node) {
					if (!$node instanceof Shine_Node_Text) {
						if ($node instanceof Shine_Node_Extends) {
							$node->nodelist[] = $block_node;
						}
						break;
					}
				}
			}
		}
		
		return $compiled_parent->render($context);
    }
}