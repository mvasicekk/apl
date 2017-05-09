<?

// prvni nastartuju session
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$direction = $o->direction;
$jenMA = $o->jenma;
$austritt60 = $o->austritt60;
$oeselected = $o->oeselected;
$statusarray = $o->statusarray;

//brutus podminka
if(
	(count($statusarray)==2 && $statusarray[0]=='MA' && $statusarray[1]=='DOHODA' )
	||
	(count($statusarray)==2 && $statusarray[1]=='MA' && $statusarray[0]=='DOHODA' )
	||
	(count($statusarray)==1 && ($statusarray[0]=='MA' || $statusarray[0]=='DOHODA' ))
  )
    {
	$austritt60 = $o->austritt60;
    }
else{
    $austritt60 = FALSE;
}

//if(count($statusarray)==1 && $statusarray[0]=='MA'){
//    $austritt60 = $o->austritt60;
//}
//else{
//    $austritt60 = FALSE;
//}

$oearray = $o->oearray;


$persinfo = NULL;
$a = AplDB::getInstance();

$u = $_SESSION['user'];



// pokud dostanu persnr = 0, vratim prvniho zamestnance se statusem MA
if (intval($persnr) == 0) {
    $sql = "select dpers.* from dpers where ";
    $sql.=" ((`dpersstatus`='MA'))";
    $sql.=" order by persnr limit 1";
} else {
    if ($direction != 0) {
	//vratit nasledujiciho/predchoziho MA , podle filtru
	$where = $direction > 0 ? " (`persnr`>'$persnr')" : " (`persnr`<'$persnr')";
	$order = $direction > 0 ? " order by persnr asc" : " order by persnr desc";
	$limit = " limit 1";
    } else {
	$where = " (`PersNr`='$persnr')";
    }

    if (count($oearray) == 1 && $oearray[0] == '*') {
	
    } else {
	$join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
	$join.=" join doe on doe.oe=dtattypen.oe";
    }
//    if($oeselected!='*'){
//	$join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
//	$join.=" join doe on doe.oe=dtattypen.oe";
//    }

    $sql = "select * from dpers";
    $sql.= " $join";
    $sql.=" where ";
    $sql.=" $where";

    // pridat filtry
    /*
      if ($jenMA==TRUE) {
      if($austritt60==TRUE){
      $sql.=" and ((dpers.eintritt is not null) and ((dpers.dpersstatus='MA') or if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0)<60))";
      }
      else{
      $sql.=" and (dpers.dpersstatus='MA')";
      }
      }
     */
    //dpersstatus
    if (is_array($statusarray)) {
	if (count($statusarray) > 0) {
	    $inStr = "( ";
	    foreach ($statusarray as $s) {
		$inStr.= "'" . $s . "'";
		$inStr.=",";
	    }
	    $inStr = substr($inStr, 0, strlen($inStr) - 1);
	    $inStr.= ")";
	    $sql.=" and (( dpers.dpersstatus IN $inStr ) ";
	    if($austritt60===TRUE){
		$sql.=" or if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),999)<60 ";
	    }
	    $sql.=" ) ";
	} else {
	    //pokud nemam zadne statusy nenajdu radeji nic
	    $sql.=" and ( false )";
	}
    }

    // pridani dalsiho filtru
    //oearray
    if (is_array($oearray)) {
	if (count($oearray) > 0) {
	    //pokud mam jen jedet tag a to jen * nebudu podminku pridavat
	    if (count($oearray) == 1 && $oearray[0] == '*') {
		
	    } else {
		$inStr = "( ";
		foreach ($oearray as $s) {
		    $inStr.= "'" . $s . "'";
		    $inStr.=",";
		}
		$inStr = substr($inStr, 0, strlen($inStr) - 1);
		$inStr.= ")";
		$sql.=" and ( doe.oe IN $inStr )";
	    }
	} else {
	    //pokud nemam zadne statusy nenajdu radeji nic
	    //$sql.=" and ( dpers.dpersstatus='8515')";
	}
    }
//    
//    if($oeselected!='*'){
//	$sql.=" and (doe.oe='$oeselected')";
//    }


    $sql.=" $order";
    $sql.=" $limit";
}

$ma = $a->getQueryRows($sql);

if ($ma !== NULL) {
    $persnrNew = $ma[0]['PersNr'];
    //upravit geboren
    $ma[0]['geborenF'] = $ma[0]['geboren'] != NULL ? date('d.m.Y', strtotime($ma[0]['geboren'])) : '';
    $ma[0]['eintrittF'] = $ma[0]['eintritt'] != NULL ? date('d.m.Y', strtotime($ma[0]['eintritt'])) : '';
    $ma[0]['austrittF'] = $ma[0]['austritt'] != NULL ? date('d.m.Y', strtotime($ma[0]['austritt'])) : '';
    $oeInfo = $a->getPersOEInfo($persnrNew);
    $bewerber = $a->getQueryRows("select * from dpersbewerber where persnr='$persnrNew'");
    // v pripade, ze mi neco vratim upravim nektere hodnoty, napr pole .....
    if ($bewerber !== NULL) {
	$br = $bewerber[0];

	// infoVomArray --------------------------------------------------------
	$infoVomArrayStr = $br['infoVomArray'];
	if ($infoVomArrayStr !== NULL) {
	    $infoVomArray = split(",", $infoVomArrayStr);
	    if (is_array($infoVomArray)) {
		$bewerber[0]['infoVomArray'] = $infoVomArray;
	    }
	}

	//  feahigkeitenArray --------------------------------------------------------
	$infoVomArrayStr = $br['faehigkeitenArray'];
	if ($infoVomArrayStr !== NULL) {
	    $infoVomArray = split(",", $infoVomArrayStr);
	    if (is_array($infoVomArray)) {
		$bewerber[0]['faehigkeitenArray'] = $infoVomArray;
	    }
	}
    }
    $sql = "select * from dpersdetail1 where persnr='$persnrNew'";
    $dpersdetail = $a->getQueryRows($sql);
    if($dpersdetail!==NULL){
	$dpersdetail[0]['dobaurcitaF'] = $dpersdetail[0]['dobaurcita'] != NULL ? date('d.m.Y', strtotime($dpersdetail[0]['dobaurcita'])) : '';
	$dpersdetail[0]['zkusebni_doba_dobaurcitaF'] = $dpersdetail[0]['zkusebni_doba_dobaurcita'] != NULL ? date('d.m.Y', strtotime($dpersdetail[0]['zkusebni_doba_dobaurcita'])) : '';
    }

    //prilohy k zobrazeni
    $gdatPath = "/mnt/gdat/Dat/";
	$ppaDir = $gdatPath . 'Aby 18 Mitarbeiter -/02 Arbeitsverhaltnis - Pr.smlouvy,dodatky,skonceni PP/08 Slozky_novych_MA/'."$persnrNew"."/foto/";
	$extensions = 'JPG|jpg';
	$filter = "/.*.($extensions)$/";
	$docsArray = $a->getFilesForPath($ppaDir, $filter);
	// pro obrazky zkusim vygenerovat nahledy pro rychlejsi zobrazeni na strance
	// slozka pro thumbnaily
	if (!file_exists($ppaDir . "/.thumbs")) {
	    @mkdir($ppaDir . "/.thumbs");
	}
	if ($docsArray !== NULL) {
	    foreach ($docsArray as $index => $doc) {
		$extPos = strrpos($doc['filename'], '.');
		$thumbsFilename = $ppaDir . "/.thumbs/" . substr($doc['filename'], 0, $extPos) . '.jpg';
		if (!file_exists($thumbsFilename) && ($doc['ext'] == 'JPG' || $doc['ext'] == 'PDF')) {
		    $img = new Imagick($ppaDir . "/" . $doc['filename'] . '[0]');

		    if ($doc['ext'] == 'PDF') {
			$img->setImageFormat('jpg');
			$img = $img->flattenImages();
		    }
		    $img->thumbnailimage(200, 200, TRUE);
		    if ($doc['ext'] == 'PDF') {
			$img->writeimage($thumbsFilename);
			$doc['ext'] == 'JPG';
		    } else {
			$img->writeimage($thumbsFilename);
		    }
		}
		$separatorPos = strrpos($doc['url'], '/');
		$docsArray[$index]['thumburl'] = substr($doc['url'], 0, $separatorPos) . "/.thumbs/" . substr($doc['filename'], 0, $extPos) . '.jpg';
	    }
	}


    $attArray = array(
	'att' => $att,
	'dir' => $ppaDir,
	'docsArray' => $docsArray,
    );
}




$returnArray = array(
    'u' => $u,
    'ma' => $ma,
    'bewerber' => $bewerber,
    'dpersdetail' => $dpersdetail,
    'oeinfo' => $oeInfo,
    'attArray'=>$attArray,
    'sql' => $sql,
);

echo json_encode($returnArray);
