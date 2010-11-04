<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */


/**
 * Shine_Tag
 * 
 * @category 	Shine
 * @package 	Shine_Tag
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Tag extends Shine_Tag_Abstract
{
    public function getLibrary()
    {
        return array(
    		'ifchanged' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'ifchanged')
    		),
    		
    		'cycle' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'cycle')
    		),
    		
    		'widthratio' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'widthratio')
    		),
    		
    		'templatetag' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'templatetag')
    		),
    		
    		'block' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'do_block')
    		),
    		
    		'extends' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'do_extends')
    		),
    		
    		'for' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'do_for')
    		),
    		
    		'with' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'do_with')
    		),
    		
    		'ifequal' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'ifequal')
    		),
    		
    		'ifnotequal' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'ifnotequal')
    		),
    		
    		'firstof' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'firstof')
    		),
    		
    		'now' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'now')
    		),
    		
    		'autoescape' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'autoescape')
    		),
    		
    		'comment' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'comment')
    		),
    		
    		'include' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'do_include')
    		),
    		
    		'spaceless' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'spaceless')
    		),
    		
    		'if' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'do_if')
    		),
    		
    		'load' => array(
    			'type' => 'tag',
    			'callback' => array(__CLASS__, 'load')
    		),
            
            'filter' => array(
                'type' => 'tag',
                'callback' => array(__CLASS__, 'dofilter')
            )
    	);
    }
    
    public static function load($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	foreach (array_slice($bits, 1) as $taglib) {
    		try {
    		    if (strpos($taglib, '.')) {
        		    $tmp = explode('.', $taglib);
                    foreach ($tmp as &$val) {
                        $val = ucfirst(strtolower($val));
                    }
                    $classname = implode(DIRECTORY_SEPARATOR, $tmp);
        		} else {
        		    $classname = ucfirst(strtolower($taglib));
        		}
                
                try {
                    Shine::loadTag($classname);
                } catch (Shine_Exception $e) {
                    throw new Shine_Exception_InvalidTemplateLibrary();
                }
                
    			$lib = new $classname();
    			$parser->addLibrary($lib);
    		} catch (Shine_Exception_InvalidTemplateLibrary $e) {
    			throw new Shine_Exception_SyntaxError("'$taglib' is not a valid tag library");
    		}
    	}
    	
    	return new Shine_Node_Load();
    }
    
    public static function dofilter($parser, $token)
    {
        $bits = self::split($token->contents);
        if (isset($bits[1])) {
            $rest = $bits[1];
        } else {
            throw new Shine_Exception_SyntaxError("");
        }
        
        $filter_expr = $parser->compile_filter("var|$rest");
        $nodelist = $parser->parse(array('endfilter'));
        $parser->delete_first_token();
        return new Shine_Node_Filter($filter_expr, $nodelist);
    }
    
    public static function ifchanged($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	$nodelist_true = $parser->parse(array('else', 'endifchanged'));
    	$token = $parser->next_token();
    	if ($token->contents == 'else') {
            $nodelist_false = $parser->parse(array('endifchanged'));
            $parser->delete_first_token();
    	} else {
            $nodelist_false = new Shine_Node_List();
    	}
    	return new Shine_Node_IfChanged($nodelist_true, $nodelist_false, array_slice($bits, 1));
    }
    
    public static function cycle($parser, $token) 
    {
    	$args = $token->split_contents();
    	$args_length = count($args);
    	
    	if ($args_length < 2) {
    		throw new Shine_Exception("'cycle' tag requires at least two arguments");
    	}
    	
    	if ($args_length == 2) {
    		$name = $args[1];
    		
    		if (!array_key_exists($name, $parser->_namedCycleNodes)) {
    			throw new Shine_Exception("Named cycle '$name' does not exist");
    		}
    		
    		return $parser->_namedCycleNodes[$name];
    		
    	} elseif ($args_length > 4 && $args[$args_length - 2] == 'as') {
    		$name = $args[$args_length - 1];
    		$node = new Shine_Node_Cycle(array_slice($args, 1, $args_length - 2), $name);
    		$parser->_namedCycleNodes[$name] = $node;
    	} else {
    		$node = new Shine_Node_Cycle(array_slice($args, 1));
    	}
    	return $node;
    }
    
    public static function widthratio($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	if (count($bits) != 4) {
    		throw new Shine_Exception("widthratio takes three arguments");
    	}
    	
    	list($tag, $this_value_expr, $max_value_expr, $max_width) = $bits;
    	
    	if (!is_numeric($max_width)) {
    		throw new Shine_Exception("widthratio final argument must be an integer");
    	}
    	
    	$this_value_expr = $parser->compile_filter($this_value_expr);
    	$max_value_expr = $parser->compile_filter($max_value_expr);
    	$max_width = (int) $max_width;
    	
    	return new Shine_Node_WidthRatio($this_value_expr, $max_value_expr, $max_width);
    }
    
    public static function templatetag($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	if (count($bits) != 2) {
    		throw new Shine_Exception("'templatetag' statement takes one argument");
    	}
    	
    	$tag = $bits[1];
    	
    	if (!array_key_exists($tag, Shine_Node_TemplateTag::$map)) {
    		throw new Shine_Exception("Invalid templatetag argument");
    	}
    	
    	return new Shine_Node_TemplateTag($tag);
    }
    
    public static function do_block($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	if (count($bits) != 2) {
    		throw new Shine_Exception("'block' tag takes only one argument");
    	}
    	
    	$block_name = $bits[1];
    	
    	if (is_array($parser->__loaded_blocks) && in_array($block_name, $parser->__loaded_blocks)) {
    		throw new Shine_Exception("'$bits[0]' tag with name '$block_name' appears more than once");
    	} else {
    		if (is_null($parser->__loaded_blocks)) {
    			$parser->__loaded_blocks = array($block_name);
    		} else {
    			$parser->__loaded_blocks[] = $block_name;
    		}
    	}
    	
    	$nodelist = $parser->parse(array('endblock', "endblock $block_name"));
    	$parser->delete_first_token();
    	return new Shine_Node_Block($block_name, $nodelist);
    }
    
    public static function do_extends($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	if (count($bits) != 2) {
    		throw new Shine_Exception("'extends' takes one argument");
    	}
    	
    	$parent_name = $parent_name_expr = null;
    	$parent_name = self::is_str($bits[1]);
    	
    	if (!$parent_name) {
    		$parent_name_expr = $parser->compile_filter($bits[1]);
    	}
    	$nodelist = $parser->parse();
    	
    	if ($nodelist->get_nodes_by_type('Shine_Node_Extends')) {
    		throw new Shine_Exception("'extends' cannot appear more than once in the same template");
    	}
    	
    	return new Shine_Node_Extends($nodelist, $parent_name, $parent_name_expr);
    }
    
    public static function do_for($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	$bits_count = count($bits);
    	
    	if ($bits_count < 4) {
    		throw new Shine_Exception("'for' statements should have at least four");
    	}
    	
    	$is_reversed = $bits[$bits_count - 1] == 'reversed';
    	
    	$in_index = $is_reversed ? $bits_count - 3 : $bits_count - 2;
    	
    	if ($bits[$in_index] != 'in') {
    		throw new Shine_Exception("'for' statements should use the format");
    	}
    	
    	$tmp = preg_replace("/ *, */", ',', implode(' ', array_slice($bits, 1, $in_index -1 )));
    	$loopvars = explode(',', $tmp);
    	
    	foreach ($loopvars as $var) {
    		if (!$var || strpos(' ', $var)) {
    			throw new Shine_Exception("'for' tag received an invalid argument:");
    		}
    	}
    	
    	$sequence = $parser->compile_filter($bits[$in_index + 1]);
    	$nodelist_loop = $parser->parse(array('endfor'));
    	$parser->delete_first_token();
    	return new Shine_Node_For($loopvars, $sequence, $is_reversed, $nodelist_loop);
    }
    
    public static function do_with($parser, $token) 
    {
    	$bits = $token->split_contents();
    	
    	if (count($bits) != 4 && $bits[2] != 'as') {
    		throw new Shine_Exception("$bits[0] expected format is 'value as name'");
    	}
    	
    	$var = $parser->compile_filter($bits[1]);
    	$name = $bits[3];
    	$nodelist = $parser->parse(array('endwith'));
    	$parser->delete_first_token();
    	return new Shine_Node_With($var, $name, $nodelist);
    }
    
    public static function do_ifequal($parser, $token, $negate) 
    {
    	$bits = $token->split_contents();
    	
    	if (count($bits) != 3) {
    		throw new Shine_Exception("$bits[0] takes two arguments");
    	}
    	
    	$end_tag = 'end' . $bits[0];
    	$nodelist_true = $parser->parse(array('else', $end_tag));
    	$token = $parser->next_token();
    	
    	if ($token->contents == 'else') {
    		$nodelist_false = $parser->parse(array($end_tag));
    		$parser->delete_first_token();
    	} else {
    		$nodelist_false = new Shine_Node_List(); 
    	}
    	
    	return new Shine_Node_IfEqual($bits[1], $bits[2], $nodelist_true, $nodelist_false, $negate);
    }
    
    public static function ifequal($parser, $token) 
    {
    	return self::do_ifequal($parser, $token, false);
    }
    
    public static function ifnotequal($parser, $token) 
    {
    	return self::do_ifequal($parser, $token, true);
    }
    
    public static function firstof($parser, $token) 
    {
    	$bits = $token->split_contents();
    	
    	if (count($bits) < 2) {
    		throw new Shine_Exception("'firstof' statement requires at least one argument");
    	}
    	
    	array_shift($bits);
    	
    	return new Shine_Node_FirstOf($bits);
    }
    
    public static function now($parser, $token)
    {
    	$quotes = null;
    	if (strpos($token->contents, '"')) {
    		$quotes = '"';
    	}
    	
    	if (strpos($token->contents, "'")) {
    		$quotes = "'";
    	}
    	
    	$bits = explode($quotes, $token->contents);
    	
    	if (count($bits) != 3) {
    		throw new Shine_Exception("'now' statement takes one argument");
    	}
    	
    	$format_string = $bits[1];
    	return new Shine_Node_Now($format_string);
    }
    
    public static function autoescape($parser, $token) 
    {
    	$args = self::split($token->contents);
    	
    	if (count($args) != 2) {
    		throw new Shine_Exception("'Autoescape' tag requires exactly one argument.");
    	}
    	
    	$arg = $args[1];
    	
    	if (!in_array($arg, array('on', 'off'))) {
    		throw new Shine_Exception("'Autoescape' tag requires exactly one argument.");
    	}
    	
    	$nodelist = $parser->parse(array('endautoescape'));
    	$parser->delete_first_token();
    	return new Shine_Node_AutoEscape(($arg === 'on'), $nodelist);
    }
    
    public static function comment($parser, $token) 
    {
    	$parser->skip_past('endcomment');
    	return new Shine_Node_Comment();
    }
    
    public static function do_include($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	
    	if (count($bits) != 2) {
    		throw new Shine_Exception('include tag takes one argument: the name of the template to be included');
    	}
    	
    	$path = $bits[1];
    	
    	if ($content = self::is_str($path)) {
    		return new Shine_Node_ConstantInclude($content);
    	} else {
    		return new Shine_Node_Include($path);
    	}
    }
    
    public static function spaceless($parser, $token) 
    {
    	$nodelist = $parser->parse(array('endspaceless'));
    	$parser->delete_first_token();
    	return new Shine_Node_Spaceless($nodelist);
    }
    
    public static function do_if($parser, $token) 
    {
    	$bits = self::split($token->contents);
    	unset($bits[0]);
    	if (!$bits) {
    		throw new Shine_Exception('if至少需要一个参数。');
    	}
    	
    	$bitstr = implode(' ', $bits);
    	$boolpairs = explode(' and ', $bitstr);
    	$boolvars = array();
    	
    	if (count($boolpairs) == 1) {
    		$link_type = Shine_Node_If::or_;
    		$boolpairs = explode(' or ', $bitstr);
    	} else {
    		$link_type = Shine_Node_If::and_;
    		if (strpos($bitstr, ' or ')) {
    			throw new Shine_Exception('if不能混合and和or');
    		}
    	}
    	
    	foreach ($boolpairs as $boolpair) {
    		if (strpos($boolpair, ' ')) {
    			$tmp = self::split(boolpair);
    			
    			if (count($tmp) != 2) {
    				throw new Shine_Exception('if有多除的参数');
    			}
    			
    			list($not_, $boolvar) = $tmp;
    			
    			if ( $not_ != 'not' ) {
    				throw new Shine_Exception('if不支持队not的参数');
    			}
    			
    			$boolvars[] = array(true, $parser->compile_filter($boolvar));
    		} else {
    			$boolvars[] = array(false, $parser->compile_filter($boolpair));
    		}
    	}
    	
    	$nodelist_true = $parser->parse(array('else', 'endif'));
    	$token = $parser->next_token();
    	if ($token->contents == 'else') {
    		$nodelist_false = $parser->parse(array('endif'));
    		$parser->delete_first_token();
    	} else {
    		$nodelist_false = new Shine_Node_List();
    	}
    	return new Shine_Node_If($boolvars, $nodelist_true, $nodelist_false, $link_type);
    }
}