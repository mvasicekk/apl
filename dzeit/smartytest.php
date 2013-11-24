<?php
require("../libs/Smarty.class.php");
$smarty = new Smarty;
$smarty->assign("book","nozderni koeficient");
$smarty->display('smartytest.tpl');
?>

