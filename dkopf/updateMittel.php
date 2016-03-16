<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();

$oper = $o->params->oper;
$teil = $o->params->teil;

if ($oper == "del") {
    // mazu vybrany mittel podle id
    $m = $o->params->m;
    $id = $m->id;
    $sql = "delete from dmittelteilabgnr where id='$id' limit 1";
    $ar = $a->query($sql);
}

if($oper=="add"){
    $mittelId = $o->params->mittel_id;
    $abgnr = $o->params->abgnr;
    $sql = "insert into dmittelteilabgnr (id_mittel,teil,abgnr,user) values('$mittelId','$teil','$abgnr','prg')";
    $ar = $a->query($sql);
}

//$ar = 1;
if ($ar > 0) {
    $sql = "select dmittelteilabgnr.*,dmittel.nazev,dmittel.poznamka from dmittelteilabgnr join dmittel on dmittel.id=dmittelteilabgnr.id_mittel where teil='$teil'";
    $mittel = $a->getQueryRows($sql);
    if ($mittel !== NULL) {
	$dir = $a->getArbMittelAnlagenFullPath();
	//pridat odkazy na soubory
	foreach ($mittel as $index => $m) {
	    $fileLink = '';
	    $fileName = $m['nazev'] . ".pdf";
	    $filePath = $dir . "/" . $fileName;
	    $urlPath = "/gdat/" . $a->getArbMittelAnlagenPath() . "/" . $fileName;
	    if (file_exists($filePath)) {
		$mittel[$index]['urlpath'] = $urlPath;
	    } else {
		$mittel[$index]['urlpath'] = "";
	    }
	}
    }
}




$returnArray = array(
    'oper'=>$oper,
    'ar' => $ar,
    'mittel' => $mittel
);

echo json_encode($returnArray);
