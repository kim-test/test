<?php
class Member extends Memberbase {

    var $_user_id;

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        $a='Hello,world2!';
        $this->assign('a', $a);
        $this->display('member.index.html');
    }

}

?>
