<?
require_once '../security.php';
?>
<?
include "../db.php";
require("../libs/Smarty.class.php");
$smarty = new Smarty;

	$dnyvTydnu = array('Ne','Po','Út','St','Čt','Pá','So');
	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}

	$a = AplDB::getInstance();
	
	$kundeVon = 111;
	$kundeBis = 999;
	//
	
	
	$pocetDnuPredAktualnimDnem = 1;
	
	$cislodnePred7dny = date('w',  time()-$pocetDnuPredAktualnimDnem*7*60*60);
	if($cislodnePred7dny==1){
	    // trefil jsem pondeli
	    $pocetDnuPredAktualnimDnem = 7;
	}
	else{
	    if($cislodnePred7dny>1){
		// utery az sobota
		$pocetDnuPredAktualnimDnem = 7+($cislodnePred7dny-1);
	    }
	    else{
		//nedele a konec
		$pocetDnuPredAktualnimDnem = 7+6;
	    }
	}
	
	//$pocetDnuPredAktualnimDnem = 14;
	
	//$pocetDnuPredAktualnimDnem = 7;
	$datumVon = date('Y-m-d',  time()-$pocetDnuPredAktualnimDnem*24*60*60);
	$datumVon = date('Y-m-d',  mktime(01, 01, 01, 5, 20,2016));
	// + 14 dnu
	$pocetdnu = $pocetDnuPredAktualnimDnem+34;
	//mkti
	$bisCustom = mktime(23, 59, 59, 6, 30,2016);
	//$konecRokuTime = mktime(23, 59, 59, 4, 31,2016);
	$datumBis = date('Y-m-d',  $bisCustom);
	$datetime1 = new DateTime($datumBis);
	$datetime2 = new DateTime($datumVon);
	$interval = $datetime1->diff($datetime2);
	$pocetdnu = $interval->format('%a')+1;
	
//	$datumBis = date('Y-m-d',  strtotime($datumVon)+$pocetdnu*24*60*60);
	
	// vytazeni dat o nakladacich
	//lkwArray
	$lkwDatumArray = array();
	$lkwDatumArrayDB = $a->getLkwDatumArray($datumVon,$datumBis);
	if($lkwDatumArrayDB!==NULL){
	    foreach ($lkwDatumArrayDB as $lkwRow){
		//zjistit imex
		$imexArray = $a->getRundlaufImExArray($lkwRow['id']);
		$imexStr = "";
		if($imexArray!==NULL){
		    foreach ($imexArray as $imex){
			$auftrStr = substr($imex['auftragsnr'],4);
			$imexStr.= "<span style='border:1px solid black;padding:0.1em;' class='payLoad_".$imex['imex']."'>".$auftrStr."</span>";
		    }
		}

		$ab_aby = $lkwRow['ab_aby'];
		$an_aby = $lkwRow['an_aby'];
		$lkwRow['imexstr'] = $imexStr;
		
		if(strlen(trim($ab_aby))>0){
		    if(!is_array($lkwDatumArray[$ab_aby])){
			$lkwDatumArray[$ab_aby] = array();
		    }
		    array_push($lkwDatumArray[$ab_aby], $lkwRow);
		}
//		if(strlen(trim($an_aby))>0){
//		    if(!is_array($lkwDatumArray[$an_aby])){
//			$lkwDatumArray[$an_aby] = array();
//		    }
//		    if($ab_aby!=$an_aby){
//			array_push($lkwDatumArray[$an_aby], $lkwRow);
//		    }
//		}
	    }
	}
	
//	AplDB::varDump($lkwDatumArray);
	
	$kundenNrArray = array();
	$importeDatumArray = array();
	$importeDatumArrayDB = $a->getImporteDatumKunde($kundeVon,$kundeBis,$datumVon,$datumBis);
	if($importeDatumArrayDB!==NULL){
	    foreach ($importeDatumArrayDB as $imRow){
		$importDatum = $imRow['import_datum'];
		$kunde = $imRow['kunde'];
		$kundenNrArray[$kunde]+=1;
		if(!is_array($importeDatumArray[$importDatum][$kunde])){
		    $importeDatumArray[$importDatum][$kunde] = array();
		}
		$draggable=$imRow['ausliefer_datum']=='noex'&&$imRow['fertig']=='norech'?'draggable':'';
		$draggable='draggable';
		array_push($importeDatumArray[$importDatum][$kunde]
			,array(
			    'kunde'=>$imRow['kunde'],
			    'import'=>$imRow['import'],
			    'bestellnr'=>$imRow['bestellnr'],
			    'im_soll_datum'=>$imRow['im_soll_datum'],
			    'im_soll_time'=>$imRow['im_soll_time'],
			    'vzkdsoll_import'=>$a->getVzKdSollImport($imRow['import']),
			    'draggable'=>$draggable,
			    'imauto'=>$a->isAuftragImRundlauf($imRow['import'],'I')?'imauto':'',
			)
			);
	    }
	}
	
	$exporteDatumArray = array();
	$exporteDatumArrayDB = $a->getExporteDatumKunde($kundeVon,$kundeBis,$datumVon,$datumBis);
	if($exporteDatumArrayDB!==NULL){
	    foreach ($exporteDatumArrayDB as $exRow){
		$exportDatum = $exRow['export_datum'];
		$kunde = $exRow['kunde'];
		$kundenNrArray[$kunde]+=1;
		if(!is_array($exporteDatumArray[$exportDatum][$kunde])){
		    $exporteDatumArray[$exportDatum][$kunde] = array();
		}
		$vzkdRest = $a->getRestVzkdForEx($exRow['export']);
		$draggable=$exRow['ausliefer_datum']=='noex'&&$exRow['fertig']=='norech'?'draggable':'';
		$draggable='draggable';
		array_push($exporteDatumArray[$exportDatum][$kunde]
			,array(
			    'kunde'=>$exRow['kunde'],
			    'vzkdrest'=>$vzkdRest,
			    'export'=>$exRow['export'],
			    'auslief'=>$exRow['ausliefer_datum'],
			    'fertig'=>$exRow['fertig'],
			    'draggable'=>$draggable,
			    'zielort'=>$exRow['zielort'],
			    'exporttime'=>$exRow['export_time'],
			    'imauto'=>$a->isAuftragImRundlauf($exRow['export'],'E')?'imauto':'',
			)
			);
	    }
	}
	
	ksort($kundenNrArray);
	
	$calendarArray = array();
	$den = 0;
	$dnesDatum = date('Y-m-d');
	while($den<=$pocetdnu){
	    $t = strtotime($datumVon)+$den*24*60*60;
	    $datum = date('Y-m-d',$t);
	    $calendarArray[$datum] = array(
		'datum'=>$datum,
		'tagname'=>$dnyvTydnu[date('w',$t)],
		'dnes'=>$dnesDatum==$datum?'dnes':''
		);
	    $den++;
	}
	
	
	$smarty->assign("importeDatumArray",$importeDatumArray);
	$smarty->assign("exporteDatumArray",$exporteDatumArray);
	$smarty->assign("lkwDatumArray",$lkwDatumArray);
	$smarty->assign("calendarArray",$calendarArray);
	$smarty->assign("kundenArray",$kundenNrArray);
	$smarty->assign("kundeVon",$kundeVon);
	$smarty->assign("kundeBis",$kundeBis);
	$smarty->assign("datumVon",$datumVon);
	$smarty->assign("datumBis",$datumBis);

	$smarty->display('imexcalendar.tpl');
?>
