<?php

define('INCLUDE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'shine_engine');

set_include_path(get_include_path(). PATH_SEPARATOR . INCLUDE_PATH);

// A Wrapper for Shine Template Engine
class CoolEngine {

    private $context = array();

    public function assign($tpl_var, $value)
    {
        if (is_array($tpl_var))
        {
            foreach ($tpl_var AS $key => $val)
            {
                if ($key != '')
                {
                    $this->context[$key] = $val;
                }
            }
        }
        else
        {
            if ($tpl_var != '')
            {
                $this->context[$tpl_var] = $value;
            }
        }
    }

    public function display($file)
    {
        require_once 'Shine.php';

        $template_path = ROOT_PATH . '/themes';

        $config = array(
            'enableCache' => false,
            'defaultContext' => array(
                'foobar' => 'abcd'
            )
        );
        
        Shine::setTemplate($template_path, $config);
        Shine::addPlugin('customtags');
	
        $shine = new Shine($file);
		
        echo $shine->render($this->context);
    }

    public function fetch($file)
    {
        var_dump($file);
        return 'test';
    }
}

?>
