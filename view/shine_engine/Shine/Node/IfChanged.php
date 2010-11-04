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

/**
 * IfChanged
 * 
 * @category	Shine
 * @package 	Shine_Node
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */

class Shine_Node_IfChanged extends Shine_Node_Abstract
{
	public $nodelist_true = null;
	
	public $nodelist_false = null;
	
	protected $_varlist = array();
	
	protected $_last_seen = null;
	
	protected $_id = null;

    function __construct($nodelist_true, $nodelist_false, $varlist)
    {
    	$this->nodelist_true = $nodelist_true;
		$this->nodelist_false = $nodelist_false;
		foreach ($varlist as $val) {
			$this->_varlist[] = new Shine_Variable($val);
		}
		$this->_id = time();
    }

    /**
     * @see Shine_Node_Abstract::render()
     */
    public function render($context)
    {
    	if (array_key_exists('forloop', $context) && !array_key_exists($this->_id, $context['forloop'])) {
    		$this->_last_seen = null;
            $context['forloop'][$this->_id] = 1;
    	}
		
		try {
			if ($this->_varlist) {
				$compare_to = array();
				foreach ($this->_varlist as $val) {
					$compare_to[] = $val->resolve($context);
				}
			} else {
				$compare_to = $this->nodelist_true->render($context);
			}
		} catch (Shine_Exception_VariableDoesNotExist $e) {
			$compare_to = null;
		}
		
		if ($compare_to != $this->_last_seen) {
			$firstloop = is_null($this->_last_seen);
			$this->_last_seen = $compare_to;
			$context->push();
			$context['ifchanged'] = array('firstloop' => $firstloop);
			$content = $this->nodelist_true->render($context);
			$context->pop();
			return $content;
		} elseif ($this->nodelist_false) {
			return $this->nodelist_false->render($context);
		}
		
		return '';
    }
}