<?php
//判断是否有post递交
define('IS_POST', (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'));
function start_session()
{
    $a=isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : md5(time());
    session_id($a);
    session_start();
}
function dump($arr)
{
    $args = func_get_args();
    print_r($args);
    exit;
}
function cool_engine()
{
	# Create a cool template engine wrapper for Shine.
	require_once(ROOT_PATH . '/view/cool_engine.php');

	return new CoolEngine();
}

class my_mysql
{
    function connect()
    {
        $con=mysql_connect("localhost","root","123456");
        if(!$con)
        {
           die('Could not connect: ' . mysql_error());
        }
        if (!mysql_select_db('test',$con))
        {
           die ("Can\'t use test : " . mysql_error());
        }
        return $con;
    }
    function get($sql)
    {
        $con=$this->connect();
        $res=mysql_query($sql,$con);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $arr[] = $row;
            }

            return $arr;
        }
        else
        {
            return false;
        }        
    }
    function auth($user_name='',$password='')
    {
        $data=array();
        $sql="SELECT * FROM users WHERE user_name='".$user_name."'";
        $data=$this->get($sql);
        if($data['0']['password'] == $password)
        {
            return $data['0']['user_id'];
        } else {
            return false;
        }
    }
}

?>
