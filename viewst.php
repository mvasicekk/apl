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

//   ./showquery1.php?label={$q.buttonName}&sql={$q.sql}&filter={$q.filter}
// vytahnu si informace o dotazu
$queryInfo = $a->getSchlTabellenArray($_POST['query']);
if($queryInfo!==NULL){
    $queryInfo = $queryInfo[0];
    // v sql dotazu nahradit jmena promennych jejich hodnotami
    $sql = $queryInfo['sql'];
    AplDB::varDump($sql);
    //vyhledam vsechny nazvy promennych v sql dotazu
    $pattern = '/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    $subject = $sql;
    $result = preg_match_all($pattern, $subject, $matches);
//    AplDB::varDump($result);
//    AplDB::varDump($matches);
    $varNamesArray = $matches[1];
//    AplDB::varDump($varNamesArray);
    foreach ($varNamesArray as $varName){
	$varValue = $_POST[$varName];
	//hack pokud bude hodnota odpovidat vzorku datumu DD.MM.YYYY -> preformatovat na db datum YYYY-MM-DD
	$pattern = '/[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}/';
	if(preg_match($pattern, $varValue)>0){
	    $varValue = $a->make_DB_datum($varValue);
	}
	//vymenit hvezdicku za procenta
	$varValue = strtr($varValue, '*', '%');
	$sql = str_replace('$'.$varName, $varValue, $sql);
//	echo "$sql<br>";
    }
//    exit();

    $parameters = $_POST;
    foreach($parameters as $var=>$value)
    {
	// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
	if(strpos($var,"_label"))
	{
		$p[$value]=$last_value;
		$par.= "$value: $last_value ";
	}
	$last_value=$value;
	//$promenne.=$var."=".$value."&";
    }

    $sql = base64_encode($sql);
    //$urlparams = "par=".urlencode($par)."&label=".urlencode($queryInfo['buttonName'])."&sql=".rawurlencode($sql)."&filter=".urlencode($queryInfo['filter']);
    $urlparams = "par=".urlencode($par)."&label=".urlencode($queryInfo['buttonName'])."&sql=".$sql."&filter=".urlencode($queryInfo['filter'])."&tabid=".urlencode($_POST['query']);
    $reporturl="./Reports/showquery1.php?".$urlparams;
//zapisu do logu pouziti sestavy
$a->reportUsageLog($_POST['query'],$reporturl,$userpc);
//echo $reporturl;
header("Location: ".$reporturl);
}
?>

