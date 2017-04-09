<?php
ini_set('display_errors', 'on');
include "init.php";
require(__DIR__ . '/config.php');
switch ($_REQUEST['mode']){
    case 'query':
        $params = AchievementDB1::getSEFdata($config,true, $_REQUEST['ajaxQuery']);
        echo $params;
        break;
    case 'insert':
        $params = AchievementDB1::makeTestData($config);
        header("Location: employees/1");
        break;
    case 'delete':
        $params = AchievementDB1::dropDB($config);
        header("Location: employees/1");
        break;
    default:
        break;


}

?>