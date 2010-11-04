<?php
define('ROOT_PATH', dirname(__FILE__));
include ROOT_PATH.'/global.php';
include ROOT_PATH.'/base.php';
$app = isset($_GET['app']) ? $_GET['app'] : 'member';
if (!is_file(ROOT_PATH.'/'.$app.'.php'))
{
    exit('Missing controller');
}
$act = isset($_GET['act']) ? $_GET['act'] : 'index';
if (!isset($act))
{
    exit('Missing action');
}
include ROOT_PATH.'/'.$app.'.php';
$app_class=ucfirst($app);
$do = new $app_class;
$do -> $act();
?>
