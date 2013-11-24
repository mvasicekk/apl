<?php
require("libs/Smarty.class.php");
$smarty = new Smarty;
$smarty->assign("book","pokusny retez");
$smarty->display('smartytest.tpl');
?>

