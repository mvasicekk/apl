<?
session_start();
require_once '../db.php';

// vztahne neje dpos ale i teildoku, mittel (AM/MM), prilohy atd ....
$data = file_get_contents("php://input");
$o = json_decode($data);

$teil = $o->teil;


$a = AplDB::getInstance();
$sql = "select * from dpos where teil='$teil' order by `TaetNr-Aby`";
$dpos = $a->getQueryRows($sql);
		

//messmittely
$dir = $a->getArbMittelAnlagenFullPath();

$sql = "select dmittelteilabgnr.*,dmittel.nazev,dmittel.poznamka from dmittelteilabgnr join dmittel on dmittel.id=dmittelteilabgnr.id_mittel where teil='$teil'";
$mittel = $a->getQueryRows($sql);
if($mittel!==NULL){
    //pridat odkazy na soubory
    foreach ($mittel as $index=>$m){
	$fileLink = '';
	$fileName = $m['nazev'].".pdf";
	$filePath = $dir."/".$fileName;
	$urlPath = "/gdat/".$a->getArbMittelAnlagenPath()."/".$fileName;
	if(file_exists($filePath)){
	    $mittel[$index]['urlpath']=$urlPath;
	}
	else{
	    $mittel[$index]['urlpath']="";
	}
    }
}

//teildoku
$teilDokuArray = $a->getTeilDokuArray($teil);

$returnArray = array(
	'teil'=>$teil,
	'dpos'=>$dpos,
	'mittel'=>$mittel,
	'teildokuarray'=>$teilDokuArray,
    );
    
echo json_encode($returnArray);
