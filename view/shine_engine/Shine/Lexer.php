<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_Lexer
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Shine_Lexer
 * 
 * @category 	Shine
 * @package 	Shine_Lexer
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Lexer {

    const TAG_RE = "/({{.*?}}|{%.*?%}|{#.*?#})/";
    
    const FILTER_SEPARATOR = '|';

    const FILTER_ARGUMENT_SEPARATOR = ':';

    const VARIABLE_ATTRIBUTE_SEPARATOR = '.';

    const BLOCK_TAG_START = '{%';

    const BLOCK_TAG_END = '%}';

    const VARIABLE_TAG_START = '{{';

    const VARIABLE_TAG_END = '}}';

    const COMMENT_TAG_START = '{#';

    const COMMENT_TAG_END = '#}';
    
    public $template_string = null;

    public function __construct($template_string) {
        $this->template_string = $template_string;
    }

    public function tokenize() {
        $in_tag = 0;
        
        $result = array();
        
        foreach (preg_split(self::TAG_RE, $this->template_string, -1, PREG_SPLIT_DELIM_CAPTURE) as $bit) {
            
            if ($bit) {
                $result[] = $this->create_token($bit, $in_tag);
            }
            
            $in_tag = ~$in_tag;
        }
        
        return $result;
    }

    public function create_token($token_string, $in_tag) {
        if ($in_tag) {
            if (strncmp($token_string, self::VARIABLE_TAG_START, 2) === 0) {
                $token = new Shine_Token(Shine_Token::TOKEN_VAR, trim(substr($token_string, 2, strlen($token_string) - 4)));
            } elseif (strncmp($token_string, self::BLOCK_TAG_START, 2) === 0) {
                $token = new Shine_Token(Shine_Token::TOKEN_BLOCK, trim(substr($token_string, 2, strlen($token_string) - 4)));
            } elseif (strncmp($token_string, self::COMMENT_TAG_START, 2) === 0) {
                $token = new Shine_Token(Shine_Token::TOKEN_COMMENT, '');
            }
            
        } else {
            $token = new Shine_Token(Shine_Token::TOKEN_TEXT, $token_string);
        }
        
        return $token;
    }
}