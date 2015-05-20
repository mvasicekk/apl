<?
session_start();

// udelam z POST promennych retez GET promennych
$promenne="";
foreach($_POST as $var=>$value)
	$promenne.=$var."=".$value."&";

// odstranit posledni & na konci $promenne
$promenne=substr($promenne,0,strlen($promenne)-1);

//echo "print=".$_POST['tl_tisk'];

$reporturl="Reports/".$_POST['report']."_".$_POST['tl_tisk'].".php?".$promenne;
//echo $reporturl;
header("Location: ".$reporturl);

?>

