<?php

/**
 * Shine Template Engine
 * 
 * 
 * @category 	Shine
 * @package 	Shine_Context
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 * @copyright 	GuoQiang Qian <gonefish@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Shine_Context
 * 
 * @category 	Shine
 * @package 	Shine_Context
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Context implements ArrayAccess
{
	public $dicts = array();
	
	public $autoescape = null;
	
	public function __construct($dict_ = array()) 
	{
		if (!is_array($dict_)) {
			$dict_ = (array) $dict_;
		}
		
		$this->dicts[] = $dict_;
	}
	
	public function push() 
	{
		array_unshift($this->dicts, array());
	}
	
	public function offsetExists($offset)
	{
		foreach ($this->dicts as $d) {
			$result = isset($d[$offset]);
			
			if ($result === true) return true;
		}
		
		return false;
	}
	
	public function pop()
	{
		if (count($this->dicts) == 1) {
			throw new Shine_Exception_ContextPop("pop() has been called more times than push()");
		}
		
		return array_shift($this->dicts);
	}
	
	public function offsetGet($offset)
	{
		foreach ($this->dicts as $d) {
			if (array_key_exists($offset, $d)) {
				return $d[$offset];
			}
		}
		
		throw new Exception();
	}
	
	public function offsetSet($offset, $value)
	{
		$this->dicts[0][$offset] = $value;
	}
	
	public function offsetUnset($offset)
	{
		unset($this->dicts[0][$offset]);
	}
	
	public function get($key, $otherwise = null)
	{
		foreach ($this->dicts as $d) {
			if (array_key_exists($key, $d)) {
				return $d[$key];
			}
		}
		
		return $otherwise;
	}
	
	public function update($other_dict)
	{
		$other_dict = (array) $other_dict;
		$this->dicts = array_merge(array($other_dict), $this->dicts);
		return $other_dict;
	}
}