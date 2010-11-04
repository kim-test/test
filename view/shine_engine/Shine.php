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
 * Shine
 * 
 * @category 	Shine
 * @package 	Shine
 * @author 		GuoQiang Qian <gonefish@gmail.com>
 */
class Shine {
	
	const VERSION = '0.9.0';
	
    public $name = null;

    public $nodelist = null;
	
    /**
     * include信息缓冲
     * 
     * @var string
     */
	protected static $_includeBuffer = '';
    
    /**
     * 模板编译信息
     * 
     * @var array
     */
    protected static $_compileInfo = array();
	
	/**
	 * 全局缓存目录
	 * 
	 * @var string
	 */
	protected static $_cacheDir = null;
	
	/**
	 * 全局模板目录
	 * 
	 * @var array
	 */
	protected static $_templatesDir = null;
	
	/**
	 * 全局模板标记目录
	 * 
	 * @var array
	 */
	protected static $_templatetagsDir = null;
	
	/**
	 * 默认日期格式
	 * 
	 * @var string
	 */
	protected static $_dateFormat = 'N j, Y';
	
	/**
	 * 模板文件类型后缀
	 * 
	 * @var string
	 */
	protected static $_templateSuffix = '.html';
	
	/**
	 * 模板文件类型后缀长度
	 * 
	 * @var integer
	 */
	protected static $_templateSuffixCount = -5;
    
    /**
     * 默认模板变量
     * 
     * @var array
     */
    protected static $_defaultContext = array();
    
    /**
     * 是否开启模板缓存
     * 
     * @var boolean
     */
    protected static $_enableCache = true;

	protected static $_plugins = array();
	
	/**
	 * 模板实例，支持三种方式创建模板
	 * 
	 * @param string $template_file_or_string
	 */
    public function __construct($template_file_or_string)
	{
		//判断是否是有效的模板文件名
		if (self::$_templateSuffix == substr($template_file_or_string, self::$_templateSuffixCount)) {
	    	$this->nodelist = $this->getNodelist($template_file_or_string);
		} else {
	    	$this->nodelist = self::compileString($template_file_or_string);
			$this->name = '<Live Template>';
		}
    }

	public static function addPlugin($classname)
	{       
		self::loadTag($classname);
		self::$_plugins[] = new $classname();
	}

	/**
	 * 设置标准模板目录
	 * 
	 * @param string $template_dir
	 * @return void
	 */
	public static function setTemplate($template_dir, $config = array())
	{
		if (!file_exists($template_dir)) {
		    return;
        }
        
		$options = array();
		
		$dirs = array(
            'cacheDir' => 'cache', 
            'templatesDir' => 'templates', 
            'templatetagsDir' => 'templatetags'
        );
		
		foreach ($dirs as $key => $value) {
		    
            if (isset($config[$key])) {
                $tmp_dir = $config[$key];
            } else {
                $tmp_dir = $template_dir . DIRECTORY_SEPARATOR . $value;
            }
			
			if (file_exists($tmp_dir)) {
			    $config[$key] = $tmp_dir;
            } else {
                continue;
            }
		}
        
		self::config($config);
	}
	
    /**
     * 重置配置项
     * 
     * @return void 
     */
	public static function resetConfig()
	{
		self::$_cacheDir = null;
		self::$_templatesDir = null;
		self::$_templatetagsDir = null;
        self::setTemplateSuffix('.html');
        self::$_enableCache = true;
        self::$_defaultContext = array();
	}
    
	/**
	 * 设置全局变量
	 * 
	 * @param array $options
	 * @param boolean $replace
	 * @return void
	 */
	public static function config($options, $replace = false)
	{
		$options = (array) $options;
		
		foreach ($options as $key => $val) {
			switch ($key) {
				case 'cacheDir':
					if (self::$_cacheDir === null || $replace) {
					    self::$_cacheDir = $val;
                    }
					break;
				case 'templatesDir';
					if (self::$_templatesDir === null || $replace) {
					    $val = (array) $val;
                        if (!empty($val)) {
                            self::$_templatesDir = $val;
                        }
                    }
					break;
				case 'templatetagsDir';
					if (self::$_templatetagsDir === null || $replace) {
					    $val = (array) $val;
                        if (!empty($val)) {
                            self::$_templatetagsDir = $val;
                            
                            foreach ($val as $v) {
                                set_include_path(get_include_path(). PATH_SEPARATOR . $v);
                            }
                        }
                    }
                    
					break;
                case 'enableCache':
                    self::$_enableCache = (boolean) $val;
                    break;
                case 'templateSuffix':
                    self::setTemplateSuffix($val);
                    break;
                case 'defaultContext':
                    $val = (array) $val;
                    self::$_defaultContext = array_merge(self::$_defaultContext, $val);
                    break;
				default:
					break;
			}
		}
	}
	
	/**
	 * 修改模板文件后缀
	 * 
	 * @param string $name 第一个字符必须是.
	 * @return void
	 */
	public static function setTemplateSuffix($name)
	{
		if (isset($name{0}) && $name{0} === '.') {
			self::$_templateSuffix = $name;
			self::$_templateSuffixCount = -strlen($name);
		}
	}
	
	/**
	 * 编译模板
	 * 
	 * @param string $template_string
	 * @return Shine_Node_List
	 */
	public static function compileString($template_string)
	{
		$lexer = new Shine_Lexer($template_string);
	    
    	$parser = new Shine_Parser($lexer->tokenize());
		
		if (count(self::$_plugins)) {
			foreach (self::$_plugins as $lib) {
				$parser->addLibrary($lib);
			}
		}
    
    	return $parser->parse();
	}
	
	/**
	 * 检测模板文件是否存在和可读
	 * 
	 * @param string $path
	 * @return string
	 * @throws Shine_Exception
	 */
	public static function checkPath($path)
	{
		if (file_exists($path)) {
			
			if (!is_readable($path)) {
				throw new Shine_Exception('模板文件不可读: ' . $path);
			}
			
			return realpath($path);
		} else {
			throw new Shine_Exception('模板文件不存在: ' . $path);
		}
	}
    
    public static function loadTag($classname)
    {
        if (class_exists($classname, false)) {
            return;
        }
        
        $file = str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';
        
        if (!is_null(self::$_templatetagsDir)) {
            foreach (self::$_templatetagsDir as $value) {
                $file_path = $value . DIRECTORY_SEPARATOR . $file;
                if ($realpath = self::checkPath($file_path)) {
                    include_once $realpath;
                    self::$_includeBuffer .= "include_once '$realpath';\n";
                }
            }
        }

        if (!class_exists($classname, false)) {
            throw new Shine_Exception();
        }
    }
    
    public static function getCacheName($file_path, $type = 'template')
    {
        $file_map = array('template' => 'php', 'html' => 'html');
        
        if (array_key_exists($type, $file_map) && self::$_cacheDir) {
            return self::$_cacheDir . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . sha1($file_path) . ".$file_map[$type]";
        } else {
            throw new Shine_Exception('不支持的类型');
        }
    }
	
	public function getNodelist($template_file, $ignore_exception = false)
	{
	    $nodelist = null;
        
		if (!is_null(self::$_templatesDir)) {
			foreach (self::$_templatesDir as $value) {
				$file_path = $value . DIRECTORY_SEPARATOR . $template_file;
				
				//检查文件是否存在和可读
				if (self::checkPath($file_path)) {
                    
					$this->name = $template_file;
                    
                    //是否开启缓存
                    $cache_flag = self::$_enableCache && !is_null(self::$_cacheDir);
                    $file_mtime = filemtime($file_path);
                    
                    if ($cache_flag) {
                        $cache_name = self::getCacheName($file_path);
                        
						if (file_exists($cache_name)) {
							include_once $cache_name;
                            
                            // 验证模板缓存正确性
                            if (isset($_nodelist) && isset($_mtime) && $file_mtime == $_mtime) {
                                $nodelist = $_nodelist;
                            }
						}
                    }
                    
					if ($nodelist === null) {
						$nodelist = self::compileString(file_get_contents($file_path));
                        
                        if ($cache_flag) { // 尝试写缓存
                            $serialize_result = base64_encode(serialize($nodelist));
                            $tmp = "<?php\n\n";
                            $tmp .= self::$_includeBuffer . "\n";
                            $tmp .= "\$_compile_result = '$serialize_result';\n\n";
                            $tmp .= "\$_mtime = '$file_mtime';\n\n";
                            $tmp .= "\$_nodelist = unserialize(base64_decode(\$_compile_result));\n\n";
                            $tmp .= "unset(\$_compile_result);";
                            file_put_contents($cache_name, $tmp);
                        }
					}
                    
					return $nodelist;
				}
			}
		}
		
		// 在当前的上下文中查找文件
		if ($absolute_path = self::checkPath($template_file)) {
			$this->name = '<Context Template>';
			return self::compileString(file_get_contents($absolute_path));
		}
	}

    /**
     * 渲染模板
     * 
     * @param array $context
     * @return string
     */
    public function render($context = array()) {
    	
		if (!$context instanceof Shine_Context) {
		    $context = array_merge($context, self::$_defaultContext);
            
            if (isset($_SESSION)) {
                $context['session'] = $_SESSION;
            }
            
            if (isset($_COOKIE)) {
                $context['cookie'] = $_COOKIE;
            }
            
			$context = new Shine_Context($context);
		}
        
        return $this->nodelist->render($context);
    }
	
	public function getAllNodes()
	{
		$node = array();
		
		foreach ($this->nodelist as $value) {
			$node = array_merge($node, $value->getNodes());
		}
		
		return $node;
	}

	public static function autoload($class)
	{
		try {
            self::loadShineClass($class);
            return $class;
        } catch (Exception $e) {
            return false;
        }
	}
	
	public static function loadShineClass($class)
	{	
        if (strncmp('Shine', $class, 5) !== 0) {
    		require_once 'Shine/Exception.php';
            throw new Shine_Exception('不支持加载非Shine的类');
    	}

		if (class_exists($class, false)) {
            return;
        }
        
		$file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        include_once $file;
		
		self::$_includeBuffer .= "include_once '$file';\n";
		
		if (!class_exists($class, false)) {
            require_once 'Shine/Exception.php';
            throw new Shine_Exception("File \"$file\" does not exist or class \"$class\" was not found in the file");
        }
	}
    
    /**
     * 快速render
     * 
     * @return 
     * @param string $template_file_or_string
     * @param array $context
     * @param boolean $cache
     * @param boolean $output
     */
    public static function simpleRender($template_file_or_string, $context, $cache = false, $output = true)
    {
        $result = null;
        
        if ($cache) {
            $cache_name = self::getCacheName($template_file_or_string, 'html');
            if (file_exists($cache_name)) {
                $result = file_get_contents($cache_name);
                if (!$result) {
                    $result = null;
                }
            }
        }
        
        if (null === $result) {
            $t = new self($template_file_or_string);
            $result = $t->render($context);
            
            if ($cache) {
                file_put_contents($cache_name, $result);
            }
        }
        
        if ($output) {
            echo $result;
        } else {
            return $result;
        }
    }
	
    /**
     * 请确保在所有的autoload实现后加载本文件，否则无法缓存
     * 
     * @return void 
     */
    public static function enableLazyLoad()
	{
		if (!function_exists('spl_autoload_register')) {
            require_once 'Shine/Exception.php';
            throw new Shine_Exception('spl_autoload does not exist in this PHP installation');
        }
        
        $all = spl_autoload_functions();
        
        if ($all) {
            foreach ($all as $val) {
                spl_autoload_unregister($val);
            }
        }
        
		spl_autoload_register(array(__CLASS__, 'autoload'));
        
        if ($all) {
            foreach ($all as $val) {
                spl_autoload_register($val);
            }
        }
	}
}

Shine::enableLazyLoad();
Shine::loadShineClass('Shine_FilterExpression');
Shine::loadShineClass('Shine_Lexer');
Shine::loadShineClass('Shine_Parser');
Shine::loadShineClass('Shine_Token');
Shine::loadShineClass('Shine_Variable');
Shine::loadShineClass('Shine_Context');
