<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_FilterExpression
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */


/**
 * Shine_FilterExpression
 * 
 * @category 	Shine
 * @package 	Shine_FilterExpression
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_FilterExpression {

    public $token = null;

    public $var = null;
	
	public $filters = null;
	
	const FILTER_RE = "/^\"(?P<constant>[^\"\\\\]*(?:\\.[^\"\\\\]*)*)\"|^(?P<var>[\\w\\.]+)|(?:|(?P<filter_name>\\w+)(?::(?:\"(?P<constant_arg>[^\"\\\\]*(?:\\.[^\"\\\\]*)*)\"|(?P<var_arg>[\\w\\.]+)))?)/";

    public function __construct($token, $parser) {
        $this->token = $token;
		$matchs = array();
		$var = null;
		$filters = array();
		$upto = 0;
		preg_match_all(self::FILTER_RE, $token, $matchs);
		
		foreach ($matchs[0] as $key => $match) {
			if (!$match) continue;
			
			$start = strpos($token, $match);
			
			if ($upto !== 0) {
				$start -= 1;
			}
			
			if ($upto != $start) {
				throw new Shine_Exception_SyntaxError("Could not parse some characters");
			}
			
			if (is_null($var)) {
				$var = $matchs['var'][$key];
				$constant = $matchs['constant'][$key];
				
				if ($constant) {
					$var = str_replace('\"', '"', $constant);
				}
				
				$upto = $start + strlen($match);
				
				if (!$var) {
					throw new Shine_Exception_SyntaxError("Could not find variable at start of");
				} elseif ($var{0} == '_') {
					throw new Shine_Exception_SyntaxError("Variables and attributes may not begin with underscores");
				}
			} else {
				$filter_name = $matchs['filter_name'][$key];
				$args = array();
				$constant_arg = $matchs['constant_arg'][$key];
				$var_arg = $matchs['var_arg'][$key];
				
				if ($constant_arg) {
					$args[] = array(false, str_replace('\"', '"', $constant_arg));
				} elseif ($var_arg) {
					$args[] = array(true, new Shine_Variable($var_arg));
				}
				
				$filter_func = $parser->find_filter($filter_name);
				$filters[] = array($filter_func, $args);
				$upto = $upto + strlen($match) + 1;
			}
		}
		
		if ($upto != strlen($token)) {
			throw new Shine_Exception_SyntaxError("Could not parse the remainder");
		}
		
		$this->filters = $filters;
        $this->var = new Shine_Variable($var);
    }

    public function resolve($context, $ignore_failures = false) {
    	try {
    		$obj = $this->var->resolve($context);
    	} catch (Shine_Exception_VariableDoesNotExist $e) {
    		if ($ignore_failures) {
    			$obj = null;
    		} else {
    			$obj = '';
    		}
    	}
		
		foreach ($this->filters as $filter_info) {
			list($func, $args) = $filter_info;
			$arg_vals = array();
			
			foreach ($args as $arg_info) {
				list($lookup, $arg) = $arg_info;
				if (!$lookup) {
					$arg_vals[] = $arg;
				} else {
					$arg_vals[] = $arg->resolve($context);
				}
			}
			
			array_unshift($arg_vals, $obj);
			
			if (isset($arg_vals[1]) && $arg_vals[1] == '') {
				unset($arg_vals[1]);
			}
			
			$obj = call_user_func_array($func, $arg_vals);
		}
		
        return $obj;
    }
}