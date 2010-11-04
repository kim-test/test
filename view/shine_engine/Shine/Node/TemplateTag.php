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

class Shine_Node_TemplateTag extends Shine_Node_Abstract
{
	static public $map = array(
		'openblock' => Shine_Lexer::BLOCK_TAG_START,
		'closeblock' => Shine_Lexer::BLOCK_TAG_END,
		'openvariable' => Shine_Lexer::VARIABLE_TAG_START,
		'closevariable' => Shine_Lexer::VARIABLE_TAG_END,
		'opencomment' => Shine_Lexer::COMMENT_TAG_START,
		'closecomment' => Shine_Lexer::COMMENT_TAG_END
	);
	
	public $tagtype = null;
	
    function __construct($tagtype)
    {
    	$this->tagtype = $tagtype;
    }
	
    public function render($context)
    {
    	return self::$map[$this->tagtype];
    }
}