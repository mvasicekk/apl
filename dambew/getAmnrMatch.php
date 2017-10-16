<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$suchen = strtolower($o->suchen);
$amnrinfo = NULL;

$a = AplDB::getInstance();
		
$artikelTabelle = "eink-artikel_test";


$sql="select `$artikelTabelle`.`art-nr` as amnr,`$artikelTabelle`.`art-vr-preis` as preis,`$artikelTabelle`.`art-name1` as text,`$artikelTabelle`.`art-name2` as text1,AM_Ausgabe as ausgabe from `$artikelTabelle` where (LOWER(`art-nr`) like '%".$suchen."%' or LOWER(`$artikelTabelle`.`art-name1`) like '%".$suchen."%' or LOWER(`$artikelTabelle`.`art-name2`) like '%".$suchen."%') order by `art-nr`";
$karty = $a->getQueryRows($sql);
if($karty!==NULL){
    foreach($karty as $ind=>$karta){
	$amnr = $karta['amnr'];
	// v jakych skladech mam polozku
	$sql = "select `eink-artikel_sklad`.sklad from `eink-artikel_sklad` where amnr='$amnr' order by sklad";
	$sklRows = $a->getQueryRows($sql);
	$sklady = '';
	if($sklRows!==NULL){
	    foreach ($sklRows as $skl){
		$sklady .= $skl['sklad'].",";
	    }
	}
	if(strlen($sklady)>0){
	    $sklady = substr($sklady, 0, strlen($sklady)-1);	//odmaznout carku na konci seznamu
	}
	$karty[$ind]['sklady'] = $sklady;
    }
}

$returnArray = array(
	'karty'=>$karty,
	'suchen'=>$suchen,
	'sql'=>$sql
    );
    
echo json_encode($returnArray);
