<?php
class Member extends Memberbase {

    var $_user_id;

    function __construct()
    {
        parent::__construct();
        
    }

    function index()
    {
        $this->check();
        $a='Hello,world2!';
        $this->assign('a', $a);
        $this->display('member.index.html');
    }
    function checkname()
    {
        $name=isset($_POST['name'])?$_POST['name']:"";
        $name=fmt($name);
        $data=$this->find("user_name=".$name,"users");
        if(!empty($data))
        {
            echo "用户名已存在";
        }else
        {
            echo "OK";
        }
        
    }
}

?>
