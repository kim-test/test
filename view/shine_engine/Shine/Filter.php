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
 * Shine_Filter
 * 
 * @category 	Shine
 * @package 	Shine_Filter
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine_Filter implements Shine_Filter_Interface
{
    public function getLibrary()
    {
        return array(
            'iriencode'      => array(__CLASS__, 'iriencode'),
    		'default'        => array(__CLASS__, 'default_'),
    		'date'           => array(__CLASS__, 'date'),
    		'add'            => array(__CLASS__, 'add'),
        	'capfirst'       => array(__CLASS__, 'capfirst'),
        	'cut'            => array(__CLASS__, 'cut'),
        	'divisibleby'    => array(__CLASS__, 'divisibleby'),
        	'escape'         => array(__CLASS__, 'escape'),
        	'first'          => array(__CLASS__, 'first'),
    		'addslashes'     => array(__CLASS__, 'addslashes'),
    		'filesizeformat' => array(__CLASS__, 'filesizeformat'),
    		'get_digit'      => array(__CLASS__, 'get_digit'),
    		'fix_ampersands' => array(__CLASS__, 'fix_ampersands'),
    		'join'           => array(__CLASS__, 'join'),
    		'last'           => array(__CLASS__, 'last'),
    		'length'         => array(__CLASS__, 'length'),
    		'length_is'      => array(__CLASS__, 'length_is'),
    		'linebreaks'     => array(__CLASS__, 'linebreaks'),
    		'linebreaksbr'   => array(__CLASS__, 'linebreaksbr'),
    		'random'         => array(__CLASS__, 'random'),
    		'removetags'     => array(__CLASS__, 'removetags'),
    		'slugify'        => array(__CLASS__, 'slugify'),
    		'urlize'         => array(__CLASS__, 'urlize'),
            'lower'          => array(__CLASS__, 'lower'),
            'upper'          => array(__CLASS__, 'upper'),
            'cutstr'         => array(__CLASS__, 'cutstr')
        );
    }
    
    /**
     * 截取字符串
     * 
     * @param string $value
     * @param int $arg
     * @return string
     */
    public static function cutstr($value, $arg)
    {
        $length = (int) $arg;
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $length, 'utf-8');
        } 

        $match = array();
        $re = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        preg_match_all($re, $value, $match);
        $slice = join("", array_slice($match[0], 0, $length));
        return $slice . "…";
    }
    
    public static function lower($value)
    {
        return strtolower((string) $value);
    }
    
    public static function upper($value)
    {
        return strtoupper((string) $value);
    }
	
	public static function add($value, $arg) 
	{
		return (int) $value + (int) $arg;
	}

	public static function addslashes($value)
    {
		return addslashes($value);
	}

    public static function capfirst($value)
    {
        return ucfirst($value);
    }

    public static function center($value)
    {
	    
    }

    public static function cut($value, $arg)
    {
    	return str_replace($arg, '', $value);
    }

    public static function iriencode($value)
    {
    	return rawurlencode($value);
    }

    public static function default_($value, $arg)
    {
    	if ($value) {
    		return $value;
    	} else {
    		return $arg;
    	}
    }

    public static function first($value)
    {
    	if (is_string($value) && strlen($value) > 1) {
    		return $value{0};
    	} elseif (is_array($value) && isset($value[0])) {
    		return $value[0];
    	} else {
    		return '';
    	}
    }

    public static function escape($value)
    {
    	return htmlspecialchars($value);
    }

    public static function divisibleby($value, $arg)
    {
    	return (int) $value % (int) $arg == 0;
    }

    public static function default_if_none($value, $arg) 
    {
    	if (!is_null($value)) {
    		return $value;
    	} else {
    		return $arg;
    	}
    }

    public static function date($value, $arg = null) {
    	if (!$value) {
    		return '';
    	}
    	
    	if (is_null($arg)) {
    		$arg = "P";
    	}
    	
    	return date($arg, (int) $value);
    }

    public static function filesizeformat($bytes)
    {
    	$bytes = intval($bytes);
    	
    	if ($bytes < 1024) {
    		return sprintf('%d bytes', $bytes);
    	} elseif ($bytes < 1024 * 1024) {
    		return sprintf('%.1f KB', $bytes / 1024);
    	} elseif ($bytes < 1024 * 1024 * 1024) {
    		return sprintf('%.1f MB', $bytes / (1024 * 1024));
    	} else {
    		return sprintf('%.1f GB', $bytes / (1024 * 1024 * 1024));
    	}
    }

    public static function get_digit($value, $arg)
    {
    	$value = intval($value);
    	$arg = intval($arg);
    	
    	$value = (string) $value;
    	
    	if ($arg < 1) {
    		return $value;
    	}
    	
    	$tmp_len = strlen($value);
    	
    	if ($tmp_len < $arg) {
    		return 0;
    	} else {
    		return $value{$tmp_len - $arg};
    	}
    }

    public static function fix_ampersands($value)
    {
    	return preg_replace("/&(?!(\w+|#\d+);)/", '&amp;', $value);
    }

    public static function join($value, $arg)
    {
    	$value = (array) $value;
    	return implode($arg, $value);
    }

    public static function last($value)
    {
    	$value = (array) $value;
    	return array_pop($value);
    }

    public static function length($value)
    {
    	if (is_array($value)) {
    		return count($value);
    	} elseif (is_string($value)) {
    		return strlen($value);
    	} else {
    		return 0;
    	}
    }

    public static function length_is($value, $arg)
    {
    	$result = false;
    	
    	if (is_array($value)) {
    		$result = count($value) == (int) $arg;
    	} elseif (is_string($value)) {
    		$result = strlen($value) == (int) $arg;
    	}
    	
    	if ($result) {
    		return 'True';
    	} else {
    		return 'False';
    	}
    }

    public static function linebreaks($value)
    {
    	$value = (string) $value;
    	$value = str_replace(array("\r\n", "\r", "\r"), "\n", $value);
    	$paras = preg_split("\n{2,}", $value);
    	$_tmp = array();
    	
    	foreach ($paras as $val) {
    		$_tmp[] = '<p>' . str_replace("\n", '<br />', trim($val)) . '</p>';
    	}
    	
    	return implode("\n\n", $_tmp);
    }

    public static function linebreaksbr($value)
    {
    	$value = (string) $value;
    	return str_replace("\n", '<br />', $value);
    }

    public static function random($value)
    {
    	if (is_array($value)) {
    		shuffle($value);
    		return array_pop($value);
    	} elseif (is_string($value)) {
    		$n = mt_rand(0, strlen($value) - 1);
    		return $value{$n};
    	} else {
    		return mt_rand();
    	}
    }

    public static function removetags($value, $tags)
    {
    	$tags = Shine_Tag::split($tags);
    	$tags_re = '(' . implode('|', $tags) . ')';
    	$value = preg_replace("/<" . $tags_re . "(\/?>|(\s+[^>]*>))/", '', $value);
    	$value = preg_replace("/<\/" . $tags_re . ">/", '', $value);
    	return $value;
    }

    public static function slugify($value)
    {
    	return preg_replace("/[-\s]+/", '-', strtolower(trim(preg_replace("/[^\w\s-]/", '', $value))));
    }

    public static function urlize($value)
    {
    	$words = preg_split("/\s+/", $value);
    	$i = 0;
    	
    	$punctuation_re = "/^(?P<lead>(?:%\(|<|&lt;)*)(?P<middle>.*?)(?P<trail>(?:\.|,|\)|>|\n|&gt;)*)$/";
    	
    	foreach ($words as $val) {
    		$match = null;
    		
    		if (strpos($val, '.') || strpos($val, '@') || strpos($val, ':')) {
    			preg_match($punctuation_re, $val, $match);
    		}
    		
    		if ($match) {
    			$lead = $match['lead'];
    			$middle = $match['middle'];
    			$trail = $match['trail'];
    			$trimmed = $match['middle'];
    			
    			$url = null;
    			
    			if (preg_match("/^https?:\/\//", $middle)) {
    				$url = $middle;
    			} elseif (preg_match("/^www\./", $middle)) {
    				$url = 'http://' . $middle;
    			} elseif (strpos($middle, '@') && !strpos($middle, ':')) {
    				$url = 'mailto:' . $middle;
    			}
    			
    			if ($url) {
    				$middle = '<a href="' . $url . '">' . $trimmed . '</a>';
    				$words[$i] = $lead . $middle . $trail;
    			} else {
    				$words[$i] = strip_tags($val);
    			}
    		} else {
    			$words[$i] = strip_tags($val);
    		}
    		
    		$i++;
    	}
    	
    	return implode(' ', $words);
    }
}