<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_Token
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Shine_Token
 * 
 * @category 	Shine
 * @package 	Shine_Token
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Token {

    const TOKEN_TEXT = 0;
    
    const TOKEN_VAR = 1;
    
    const TOKEN_BLOCK = 2;
    
    const TOKEN_COMMENT = 3;
	
	const SMART_SPLIT = "/(\"(?:[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*)\"|'(?:[^'\\\\]*(?:\\\\.[^'\\\\]*)*)'|[^\\s]+)/";
    
    public $contents = null;
    
    public $token_type = null;

    public function __construct($token_type, $contents) {
        $this->contents = $contents;
        $this->token_type = $token_type;
    }

    public function split_contents() {
        $split = array();
		preg_match_all(self::SMART_SPLIT, $this->contents, $split);
		
		foreach ($split[0] as &$val) {
			if ($val{0} == '"' && $val{strlen($val)-1} == '"') {
				
			} elseif ($val{0} == "'" && $val{strlen($val)-1} == "'") {
				$val = str_replace("'", '"', $val);
			}
		}
		
        return $split[0];
    }
}