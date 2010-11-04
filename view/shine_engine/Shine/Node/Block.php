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

class Shine_Node_Block extends Shine_Node_Abstract
{
	public $name = null;
	
	public $nodelist = null;
	
	public $parent = null;
	
	public $context = null;
	
    function __construct($name, $nodelist, $parent = null)
    {
    	$this->name = $name;
		$this->nodelist = $nodelist;
		$this->parent = $parent;
    }
	
    public function render($context)
    {
    	$context->push();
		$this->context = $context;
		$context['block'] = $this;
		$result = $this->nodelist->render($context);
		$context->pop();
		return $result;
    }
	
	public function super()
	{
		if ( $this->parent ) {
			return $this->parent->render($this->context);
		}
		return '';
	}
	
	public function add_parent($nodelist)
	{
		if ( $this->parent ) {
			$this->parent->add_parent($nodelist);
		} else {
			$this->parent = new Shine_Node_Block($this->name, $this->nodelist);
		}
	}
}

?>