<?php
session_start();
require("../libs/Smarty.class.php");
$smarty = new Smarty;

// varianta bez autorizace, pro screenly
$smarty->display("pers.tpl");
