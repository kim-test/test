<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_Parser
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */


/**
 * Shine_Parser
 * 
 * @category 	Shine
 * @package 	Shine_Parser
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Parser {
	
    public $tokens = null;

    public $tags = array();

    public $filters = array();
	
	public $__loaded_blocks = null;
	
	public $_namedCycleNodes = array();
	
    public function __construct($tokens) {
        $this->tokens = $tokens;
        
        $a = new Shine_Filter();
        $this->filters = $a->getLibrary();
        $b = new Shine_Tag();
        $this->tags = $b->getLibrary();
    }
    
    public static function inclusion_tag_compiler($parser, $token, $funcName, $template) 
    {
    	$bits = $token->split_contents();
    	$bits = array_slice($bits, 1);
    	return new Shine_Node_Inclusion($bits, $funcName, $template);
    }
    
    public static function simple_tag_compiler($parser, $token, $funcName) 
    {
    	$bits = $token->split_contents();
    	$bits = array_slice($bits, 1);
    	return new Shine_Node_Simple($bits, $funcName);
    }
	
	public static function callTagFunc($tagInfo, $params)
	{
		$type = $tagInfo['type'];
		$func = $tagInfo['callback'];
		$node = null;
		switch ($type) {
			case 'simpletag':
				$params[] = $func;
				$node = call_user_func_array(array(__CLASS__, 'simple_tag_compiler'), $params);
				break;
			case 'tag':
				$node = call_user_func_array($func, $params);
				break;
			case 'inclusiontag':
				$params[] = $func;
				$params[] = $tagInfo['template'];
				$node = call_user_func_array(array(__CLASS__, 'inclusion_tag_compiler'), $params);
				break;
			default:
				throw new Shine_Exception_InvalidTemplateLibrary();
		}
		
		return $node;
	}

    public function parse($parse_until = null) {
        
        if (is_null($parse_until)) {
            $parse_until = array();
        }
        
        $nodelist = $this->create_nodelist();
        
        while ($this->tokens) {
            
            $token = $this->next_token();
            
            if ($token->token_type == Shine_Token::TOKEN_TEXT) {
                
                $this->extend_nodelist($nodelist, new Shine_Node_Text($token->contents), $token);
                
            } elseif ($token->token_type == Shine_Token::TOKEN_VAR) {
                
                if (!$token->contents) {
                    $this->empty_variable($token);
                }
                
                $filter_expression = $this->compile_filter($token->contents);
                
                $var_node = $this->create_variable_node($filter_expression);
                
                $this->extend_nodelist($nodelist, $var_node, $token);
            } elseif ($token->token_type == Shine_Token::TOKEN_BLOCK) {
            	//var_dump($token->contents);
            	//var_dump($parse_until);
            	
            	
                if (in_array($token->contents, $parse_until)) {
                	$this->prepend_token($token);
                    return $nodelist;
                }
                
                $tmp = explode(' ', $token->contents);
                $command = $tmp[0];
                
                if ($command === '') {
                	$this->empty_block_tag($token);
                }
                
                if (array_key_exists($command, $this->tags)) {
                	$compile_func = $this->tags[$command];
                } else {
                	$this->invalid_block_tag($token, $command);
                }
                
                try {
                	//$compiled_result = call_user_func_array($compile_func, array($this, $token));
                	$compiled_result = self::callTagFunc($compile_func, array($this, $token));
                } catch (Shine_Exception $e) {
                	//echo 'error';
                	echo $e;
                }
                
                $this->extend_nodelist($nodelist, $compiled_result, $token);
            }
        }

        if ($parse_until) {
            $this->unclosed_block_tag($parse_until);
        }
    
        return $nodelist;
    }
    
    public function invalid_block_tag($token, $command) {
    	throw $this->error($token, "Invalid block tag: '$command'");
    }
    
    public function skip_past($endtag) {
    	while ($this->tokens) {
    		$token = $this->next_token();
    		
    		if ($token->token_type == Shine_Token::TOKEN_BLOCK && $token->contents == $endtag) {
    			return;
    		}
    	}
    	
    	$this->unclosed_block_tag(array($endtag));
    }
	
	public function find_filter($filter_name)
    {
		if (array_key_exists($filter_name, $this->filters)) {
			return $this->filters[$filter_name];
		} elseif (function_exists($filter_name)) {
			return $filter_name;
		} else {
			throw new Shine_Exception_SyntaxError("Invalid filter: '$filter_name'");
		}
	}
    
    public function prepend_token($token) {
    	array_unshift($this->tokens, $token);
    }
    
    public function empty_block_tag($token) {
    	throw $this->error($token, 'Empty block tag');
    }
        
    public function addLibrary($lib) {
        
        if ($lib instanceof Shine_Tag_Abstract) {
            $this->tags = array_merge($this->tags, $lib->getLibrary());
        }
        
        if ($lib instanceof Shine_Filter_Interface) {
            $this->filters = array_merge($this->filters, $lib->getLibrary());
        }
    }

    public function compile_filter($token) {
        return new Shine_FilterExpression($token, $this);
    }

    public function unclosed_block_tag($parse_until) {
    	$tmp = implode(', ', $parse_until);
		throw $this->error($token, 'Empty variable tag: ' . $tmp);
    }

    public function empty_variable($token) {
		throw $this->error($token, 'Empty variable tag');
    }

    public function error($token, $msg) {
    	return new Shine_Exception($msg);
    }

    public function extend_nodelist($nodelist, $node, $token) {
    	if ($node->isFirst() && $nodelist && $nodelist->contains_nontext) {
			throw new Shine_Exception_SyntaxError("must be the first tag in the template.");
    	}
		
        if ($nodelist instanceof Shine_Node_List && (!$node instanceof Shine_Node_Text)) {
            $nodelist->contains_nontext = true;
        }

        $nodelist[] = $node;
    }

    public function next_token() {
        return array_shift($this->tokens);
    }
    
    public function delete_first_token() {
    	unset($this->tokens[0]);
    }

    public function create_nodelist() {
        return new Shine_Node_List();
    }

    public function create_variable_node($filter_expression) {
        return new Shine_Node_Variable($filter_expression);
    }
}