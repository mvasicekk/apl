<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$jahr = $o->jahr;
$monat = $o->monat;
$jenma = $o->jenma;
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

$kriterienArray=array(
    array(
	'oe'=>'G11',
	'grenzedown'=>0.2,
	'grenzeup'=>0.31,
	'grenze_reparatur'=>1,
	'pct_plus'=>10,
	'pct_minus'=>10,
	'pct_reparatur'=>25
    ),
    array(
	'oe'=>'G51',
	'grenzedown'=>0.05,
	'grenzeup'=>0.15,
	'grenze_reparatur'=>1,
	'pct_plus'=>10,
	'pct_minus'=>10,
	'pct_reparatur'=>25
    ),
);

// vybrat lidi
$where = "";
$join = "";

if ($oeselected != '*') {
    $where.=" and (dtattypen.oe='$oeselected')";
}

if ($jenma==TRUE) {
    $where.=" and (dpers.dpersstatus='MA')";
}

if ($oeselected != '*') {
    $join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
}

$sql = "select dpers.persnr from dpers";
$sql.= " $join";
$sql.=" where (1)";
$sql.=" $where";
$sql.=" order by dpers.persnr";

$persnrArray = $a->getQueryRows($sql);

if ($persnrArray !== NULL) {
    // projit seznam ma podle nastaveneho filtru oeselected, jenma
    foreach ($persnrArray as $pr) {
	$persnr = $pr['persnr'];
	if ($lock == TRUE) {
	    //zamknout vybrane
	    $sql = "select id from dperspremie where (persnr='$persnr') and (id_premie=8) and (datum='$von')";
	    $prs = $a->getQueryRows($sql);
	    $l = $lockvalue==0?"apl_unlocked_von_$u":"apl_locked_von_$u";
	    if ($prs !== NULL) {
		//radek uz mam, zamknu
		$id = $prs[0]['id'];
		$upd = "update dperspremie set locked='$lockvalue',last_edit='$l' where (id='$id')";
		$a->query($upd);
	    } else {
		//radek jeste nemam, provedu insert
		$id_premie = 8; // hf_reparaturen_premie
		$sql = "insert into dperspremie (persnr,datum,betrag,id_premie,last_edit,locked)";
		$sql.=" values('$persnr','$von','0','$id_premie','$l','$lockvalue')";
		$insertId = $a->insert($sql);
	    }
	} else {
	    $hfPremieArray = $a->getHFPremieArray($von1, $bis1, $persnr, $persnr, $faktorup, $faktordown, $premiepct, $kriterienArray);
	    if ($hfPremieArray !== NULL) {
		foreach ($hfPremieArray as $hfp) {
		    foreach ($hfp['monate'] as $jm => $prA) {
			$premie = $prA['premie'];
			//mam uz premii v tabulce
			$sql = "select id from dperspremie where (persnr='$persnr') and (id_premie=8) and (datum='$von')";
			$prs = $a->getQueryRows($sql);
			if ($prs !== NULL) {
			    //radek uz mam, betrag aktualizuju na vypoctenou hodnotu, ale jen pokud je locked = 0
			    $id = $prs[0]['id'];
			    $upd = "update dperspremie set betrag='$premie',last_edit='apl_fullen_von_$u' where (id='$id') and (locked=0)";
			    $a->query($upd);
			} else {
			    //radek jeste nemam, provedu insert
			    $id_premie = 8; // hf_reparaturen_premie
			    $sql = "insert into dperspremie (persnr,datum,betrag,id_premie,last_edit)";
			    $sql.=" values('$persnr','$von','$premie','$id_premie','apl_fullen_von_$u')";
			    $insertId = $a->insert($sql);
			}
		    }
		}
	    }
	}
    }
}




$returnArray = array(
    'persnrArray'=>$persnrArray,
    'u' => $u,
    'von'=>$von1,
    'bis'=>$bis1,
    'hfpremiearray' => $hfPremieArray,
    'sql' => $sql,
);

echo json_encode($returnArray);
