<?php
require_once '../security.php';

require("../libs/Smarty.class.php");
require_once '../db.php';


$smarty = new Smarty;

require_once '../assignsecurity.php';


//$sql = rawurldecode($_GET['sql']);
$sql = base64_decode($_GET['sql']);

//echo "$sql";
//exit();

$par = urldecode($_GET['par']);
$label = urldecode($_GET['label']);
$filter = urldecode($_GET['filter']);
$tabid = urldecode($_GET['tabid']);

$smarty->assign("user", $_SESSION['user']);
$smarty->assign("level", $_SESSION['level']);
$smarty->assign("prihlasen", 1);

$smarty->assign("sql",$sql);
$smarty->assign("tabid",$tabid);
$smarty->assign("par",$par);
$smarty->assign("label",$label);
$smarty->assign("filter",$filter);
$smarty->display('showquery1.tpl');