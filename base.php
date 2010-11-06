<?php
class Memberbase extends Base{
    function __construct()
    {
        parent::__construct();
    }

    
}

class Base{
    var $_has_login;
    var $_view = null;
    var $_db= null;

    function __construct()
    {
        if(!session_id())
        {
            start_session();
        }
        $this->_db=new my_mysql();
        
    }

    function check()
    {
        if(isset($_SESSION['user_info']['user_id']) || $this -> _has_login)
        {
            $this -> _has_login=1;
        }else
        {
            $this->login();
            exit;
        }
    }

    function login()
    {
        if($this -> _has_login)
        {   
            header("Location:/test");
            exit;
        }
        if(!IS_POST)
        {
            $this->display('login.html');
        }
        else{
            $user_name=isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
            $password=isset($_POST['password']) ? $_POST['password'] : '';
            $user_id=$this->_db->auth($user_name,$password);
            if(!$user_id)
            {
                header("Location:/test");
                exit;
            }
            $_SESSION['user_info']['user_id']=$user_id;
            header("Location:/test");
        }
    }

    function logout()
    {
        session_destroy();
        setcookie('PHPSESSID','',0,'/','','', true);
        header("Location:/test");
    }
    function assign($k, $v = null)
    {
        $this->_init_view();
        if (is_array($k))
        {
            $args  = func_get_args();
            foreach ($args as $arg)     //遍历参数
            {
                foreach ($arg as $key => $value)    //遍历数据并传给视图
                {
                    $this->_view->assign($key, $value);
                }
            }
        }
        else
        {
            $this->_view->assign($k, $v);
        }
    }
    function _init_view()
    {
        if ($this->_view === null)
        {
            $this->_view = cool_engine();
        }
    }
    function display($n)
    {
        $this->_init_view();
        $this->_view->display($n);
    }
    function find($params=array(),$table='')
    {
        if(empty($table))
        {
            echo 'Can\'t find data!';
        }
        extract($this->initsql($params));
        $fields == '' && $fields = '*';
        $order && $order = ' ORDER BY ' . $order;
        $limit && $limit = ' LIMIT ' . $limit;
        $conditions = ' WHERE '.$conditions;
        $sql = "SELECT {$fields} FROM {$table}{$conditions}{$order}{$limit}";
        return $this->_db->get($sql);
    }
    function initsql($params)
    {
        $arr = array(
            'include'  => array(),
            'join'=> '',
            'conditions' => '1',
            'order'      => '',
            'fields'     => '',
            'limit'      => '',
            'count'      => false,
        );
        if (is_array($params))
        {
            return array_merge($arr, $params);
        }
        else
        {
            $arr['conditions'] = $params;
            return $arr;
        }
    }
}

?>
