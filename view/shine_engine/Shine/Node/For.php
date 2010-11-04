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

class Shine_Node_For extends Shine_Node_Abstract
{
	public $loopvars = null;
	
	public $sequence = null;
	
	public $is_reversed = null;
	
	public $nodelist_loop = null;
	
    function __construct($loopvars, $sequence, $is_reversed, $nodelist_loop)
    {
    	$this->loopvars = $loopvars;
		$this->sequence = $sequence;
		$this->is_reversed = $is_reversed;
		$this->nodelist_loop = $nodelist_loop;
    }
	
	public function get_nodes_by_type($nodetype) {
        $nodes = array();
        if ($this instanceof $nodetype) {
            $nodes[] = $this;
        }
		
		$nodes = array_merge($nodes, $this->nodelist_loop->get_nodes_by_type($nodetype));
        
        return $nodes;
    }
	
	public function getNodes()
	{
		$nodes = array();
		foreach ($this->nodelist_loop as $node) {
			$nodes[] = $node;
		}
		return $nodes;
	}
	
    public function render($context)
    {
    	$nodelist = new Shine_Node_List();
		
		if (isset($context['forloop'])) {
			$parentloop = $context['forloop'];
		} else {
			$parentloop = array();
		}
		
		$context->push();
		
		try {
			$values = $this->sequence->resolve($context, true);
		} catch (Shine_Exception_VariableDoesNotExist $e) {
			$values = array();
		}
		
		if (is_null($values)) {
			$values = array();
		}
		
		$len_values = count($values);
		
		if ($this->is_reversed) {
			$values = array_reverse($values);
		}
		
		$unpack = count($this->loopvars) > 1;
		
		$i = 0;
		
		foreach ($values as $key => $item) {
		    $loop_dict = array();
            
			$loop_dict['counter0'] = $i;
            $loop_dict['counter'] = $i + 1;
			
			$loop_dict['revcounter'] = $len_values - $i;
            $loop_dict['revcounter0'] = $len_values - $i - 1;
			
			$loop_dict['first'] = ($i == 0);
            $loop_dict['last'] = ($i == ($len_values - 1));
            
            $loop_dict['parentloop'] = $parentloop;
            
            $context['forloop'] = $loop_dict;
			
			if ($unpack) {
				$context->update(array($this->loopvars[0] => $key, $this->loopvars[1] => $item));
			} else {
				$context[$this->loopvars[0]] = $item;
			}
            
			foreach ($this->nodelist_loop as $node) {
				$nodelist[] = $node->render($context);
			}
			
			if ($unpack) $context->pop();
            
            $i++;
		}
		
		$context->pop();
		return $nodelist->render($context);
    }
}