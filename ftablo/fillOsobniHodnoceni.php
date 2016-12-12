<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$jahr = $o->jahr;
$monat = $o->monat;
$jenma = $o->jenma;
$austritt60 = $o->austritt60;
$oeselected = $o->oeselected;
$lock = $o->lock;
$lockvalue = $o->lockvalue;

$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$tagbis = $pocetDnuVMesici;

$von = $jahr.'-'.$monat.'-01';
$bis = $jahr.'-'.$monat.'-'.$tagbis;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$von1 = date('d.m.Y',strtotime($von));
$bis1 = date('d.m.Y',strtotime($bis));

// vybrat lidi
$where = "";
$join = "";

if ($oeselected != '*') {
    $where.=" and (dtattypen.oe='$oeselected')";
}

if ($jenma==TRUE) {
    if($austritt60==TRUE){
	$where.=" and ((dpers.eintritt is not null) and ((dpers.dpersstatus='MA') or if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0)<60))";
    }
    else{
	$where.=" and (dpers.dpersstatus='MA')";
    }
    
}

//if ($austritt60==TRUE && $jenma==TRUE) {
//    $where.=" and (dpers.dpersstatus='MA' or DATEDIFF(NOW(),dpers.austritt),0)<60)";
//}

if ($oeselected != '*') {
    $join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
}

$sql = "select dpers.persnr from dpers";
$sql.= " $join";
$sql.=" where (1)";
$sql.=" $where";
$sql.=" order by dpers.persnr";

$persnrArray = $a->getQueryRows($sql);

//$ohA = $a->getOsobniHodnoceniProPersNr(14,  $von,$bis);

$jahrMonatArray = array();
$start = strtotime($von);
$end = strtotime($bis);
$step = 24 * 60 * 60;
for ($t = $start; $t <= $end; $t+=$step) {
    $jm = date('Y-m', $t);
    $jahrMonatArray[$jm] += 1;
}


if ($persnrArray !== NULL) {
    // projit seznam ma podle nastaveneho filtru oeselected, jenma
    foreach ($persnrArray as $pr) {
	$persnr = $pr['persnr'];
	if ($lock == TRUE) {
	    //zamykani
	    $osobniFaktory = $a->getOsobniHodnoceniFaktoryProPersNr($persnr);
	    if ($osobniFaktory !== NULL) {
		foreach ($osobniFaktory as $of) {
		    $id_osobni_faktor = $of['id_faktor'];
		    $sql = "select id from hodnoceni_osobni where persnr='$persnr' and id_faktor='$id_osobni_faktor' and datum='$von'";
		    $prs = $a->getQueryRows($sql);
		    $l = $lockvalue == 0 ? "apl_unlocked_von_$u" : "apl_locked_von_$u";
		    if ($prs !== NULL) {
			//radek uz mam, zamknu
			$id = $prs[0]['id'];
			$upd = "update hodnoceni_osobni set locked='$lockvalue',last_edit='$l' where (id='$id')";
			$a->query($upd);
		    } else {
			//radek jeste nemam, provedu insert
			$sql = "insert into hodnoceni_osobni (persnr,id_faktor,datum,last_edit,locked) values('$persnr','$id_osobni_faktor','$von','$l','$lockvalue')";
			$insertId = $a->insert($sql);
		    }
		}
	    }
	} else {
	    // prevezmu hodnoceni podle firemnich hodnot
	    $osobniHodnoceniArray = $a->getOsobniHodnoceniProPersNr($persnr,  $von,$bis);
	    //$jmArray = $osobniHodnoceniArray['jahrmonatArray'];
	    
	    if($osobniHodnoceniArray!==NULL){
		foreach($osobniHodnoceniArray['osobniFaktory'] as $index=>$of){
		    $id_osobni_faktor = $of['id_faktor'];
		    $id_firma_faktor = $of['id_firma_faktor'];
		    $vaha = floatval($of['vaha']);
		    //prebiram jen firemni hodnoty, tj. $id_firma_faktor>0
		    if($id_firma_faktor>0){
			foreach ($jahrMonatArray as $jm=>$pocet){
			    $datum = $jm.'-01';
			    $hodnoceniFirma = $a->getHodnoceniFirmaFaktorDatum($id_firma_faktor,$datum);
			    if($hodnoceniFirma!==NULL){
				//mam nejake hodnoceni pro firmu
				$castka = AplDB::hodnoceni2Penize($vaha, $hodnoceniFirma);
				$sql = "select id from hodnoceni_osobni where persnr='$persnr' and id_faktor='$id_osobni_faktor' and datum='$datum'";
				$rr = $a->getQueryRows($sql);
				if($rr==NULL){
				    //nemam, vlozim
				    $insert = "insert into hodnoceni_osobni (persnr,id_faktor,datum) values('$persnr','$id_osobni_faktor','$datum')";
				    $id_hodnoceni = $a->insert($insert);
				}
				else{
				    $id_hodnoceni = $rr[0]['id'];
				}
				// update, ale jen nezamcene
				$sql = "update hodnoceni_osobni set hodnoceni='$hodnoceniFirma',castka='$castka' where (id='$id_hodnoceni') and (locked=0)";
				$ar = $a->query($sql);
			    }
			}
		    }
		}
	    }
	}
    }
}




$returnArray = array(
    'ohAexample'=>$ohA,
    'persnrArray'=>$persnrArray,
    'u' => $u,
    'von'=>$von1,
    'bis'=>$bis1,
    'hfpremiearray' => $hfPremieArray,
    'sql' => $sql,
);

echo json_encode($returnArray);
