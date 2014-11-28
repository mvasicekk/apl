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
	$pocetDnuPredAktualnimDnem = 7;
	$datumVon = date('Y-m-d',  time()-$pocetDnuPredAktualnimDnem*24*60*60);
	// + 14 dnu
	$pocetdnu = $pocetDnuPredAktualnimDnem+21;
	$datumBis = date('Y-m-d',  strtotime($datumVon)+$pocetdnu*24*60*60);
	
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
		array_push($importeDatumArray[$importDatum][$kunde]
			,array(
			    'kunde'=>$imRow['kunde'],
			    'import'=>$imRow['import'],
			    'im_soll_datum'=>$imRow['im_soll_datum'],
			    'im_soll_time'=>$imRow['im_soll_time'],
			    'vzkdsoll_import'=>$a->getVzKdSollImport($imRow['import']),
			    'draggable'=>$draggable,
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
		$draggable=$exRow['ausliefer_datum']=='noex'&&$exRow['fertig']=='norech'?'draggable':'';
		array_push($exporteDatumArray[$exportDatum][$kunde]
			,array(
			    'kunde'=>$exRow['kunde'],
			    'export'=>$exRow['export'],
			    'auslief'=>$exRow['ausliefer_datum'],
			    'fertig'=>$exRow['fertig'],
			    'draggable'=>$draggable,
			    'zielort'=>$exRow['zielort'],
			    'exporttime'=>$exRow['export_time']
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
	$smarty->assign("calendarArray",$calendarArray);
	$smarty->assign("kundenArray",$kundenNrArray);
	$smarty->assign("kundeVon",$kundeVon);
	$smarty->assign("kundeBis",$kundeBis);
	$smarty->assign("datumVon",$datumVon);
	$smarty->assign("datumBis",$datumBis);

	$smarty->display('imexcalendar.tpl');
?>
