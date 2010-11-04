<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_Variable
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Shine_Variable
 * 
 * @category 	Shine
 * @package 	Shine_Variable
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Variable {

    const VARIABLE_ATTRIBUTE_SEPARATOR = '.';

    public $var = null;

    public $literal = null;

    public $lookups = null;

    public function __construct($var) {
        $this->var = $var;
        if (is_numeric($var)) {
            $this->literal = $var;
        } else {
            if (($var{0} == '"' || $var{0} == "'") && $var{0} == $var{strlen($var)-1}) {
                $this->literal = substr($var, 1, strlen($var)-2);
            } else {
                $this->lookups = explode(self::VARIABLE_ATTRIBUTE_SEPARATOR, $this->var);
            }
        }
    }

    public function resolve($context) {
        if (!is_null($this->lookups)) {
            $value= $this->_resolve_lookup($context);
        } else {
            $value = $this->literal;
        }

        return $value;
    }

    public function _resolve_lookup($context) {
        $current = $context;
        foreach ($this->lookups as $bit) {
            if (is_array($current) && array_key_exists($bit, $current)) {
                $current = $current[$bit];
            } else {
                if (is_object($current)) {
					if ($current instanceof ArrayAccess && isset($current[$bit])) {
						$current = $current[$bit];
					} else {
						if (property_exists($current, $bit)) {
	                        $current = $current->$bit;
	                    } else {
	                        if (method_exists($current, $bit)) {
	                            $current = $current->$bit();
	                        } else {
	                        	throw new Shine_Exception_VariableDoesNotExist("Failed lookup for key" . $bit);
	                        }
	                    }
					}
                } else {
                	throw new Shine_Exception_VariableDoesNotExist("Failed lookup for key" . $bit);
                }
            }
        }

        return $current;
    }
}