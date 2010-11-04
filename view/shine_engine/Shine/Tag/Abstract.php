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
 * Shine_Tag_Abstract
 * 
 * @category 	Shine
 * @package 	Shine_Tag
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
abstract class Shine_Tag_Abstract
{
    abstract public function getLibrary(); 
    
    /**
	 * 通过空格分割字符串
	 * 
	 * 
	 * @param string $str
	 * @return 
	 */
    static public function split($str) {
    	$tmp = explode(' ', $str);
    	$has_blank = false;
    	foreach ($tmp as $key => $val) {
    		if ($val == '') {
    			if ($has_blank === false) {
    				$has_blank = true;
    			} else {
    				unset($tmp[$key]);
    			}
    		}
    	}
    	return $tmp;
    }
	
	static public function is_str($var) {
		$var = (string) $var;
		
		if (in_array($var{0}, array("'", '"')) && $var{0} === $var{strlen($var)-1}) {
			$str_content = substr($var, 1, strlen($var) - 2);
			return $str_content;
		} else {
			return null;
		}
	}
}