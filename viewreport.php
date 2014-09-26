<?
session_start();

require_once './db.php';

// udelam z POST promennych retez GET promennych
$promenne="";
foreach($_POST as $var=>$value)
	$promenne.=$var."=".$value."&";

// odstranit posledni & na konci $promenne
$promenne=substr($promenne,0,strlen($promenne)-1);

//echo "print=".$_POST['tl_tisk'];

$a = AplDB::getInstance();
$userpc = $a->get_user_pc();
$reporturl="Reports/".$_POST['report']."_".$_POST['tl_tisk'].".php?".$promenne;
//zapisu do logu pouziti sestavy
$a->reportUsageLog($_POST['report'],$reporturl,$userpc);
//echo $reporturl;
header("Location: ".$reporturl);

?>

