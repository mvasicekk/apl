<?
 session_start();
?>

<?
require_once '../db.php';

    $id=$_POST['id'];
    $imanr = $_POST['ima'];
    $maTyp = $_POST['maTyp'];

//    $imanr = "IMA_195_1510081416";
//    $maTyp = 'ima';
    
    $apl = AplDB::getInstance();
    $ar = 0;
    $user = $apl->get_user_pc();

    //1. vztahnu si informace o schvalene vicepraci
    $imaInfoArray = $apl->getIMAInfoArrayFromImaNr($imanr);
    if($imaInfoArray!==NULL){
	$ir = $imaInfoArray[0];
	$teil = $ir['teil'];
	if($maTyp=='ima'){
	    $imArray = explode(';', $ir['ima_auftragsarray_genehmigt']);
	    $palArray = explode(';', $ir['ima_palarray_genehmigt']);
	    $dauftrIdArray = explode(';', $ir['ima_dauftrid_array_genehmigt']);
	    $tatArray = explode(';', $ir['ima_tatundzeitarray_genehmigt']);
	}
	else{
	    $imArray = explode(';', $ir['ema_auftragsarray_genehmigt']);
	    $palArray = explode(';', $ir['ema_palarray_genehmigt']);
	    $dauftrIdArray = explode(';', $ir['ema_dauftrid_array_genehmigt']);
	    $tatArray = explode(';', $ir['ema_tatundzeitarray_genehmigt']);
	}
	
	if(is_array($imArray) && is_array($palArray) && is_array($tatArray) && is_array($dauftrIdArray)){
	    $palArraySkutecne = array();
	    foreach ($dauftrIdArray as $i){
		$dauftrRow = $apl->getDauftrRow($i);
		if($dauftrRow!==NULL){
		    array_push($palArraySkutecne, array('im'=>$dauftrRow['auftragsnr'],'pal'=>$dauftrRow['pal'],'stk'=>$dauftrRow['stk'],'pos'=>$dauftrRow['fremdpos']));
		}
	    }

//	    echo "palArraySkutecne<hr>";
//	    AplDB::varDump($palArraySkutecne);
	    
	    //vytvorim si pole se skutecnyma pozicema k vytvoreni
	    $imPalTatArrayToCreate = array();
	    foreach ($palArraySkutecne as $ps){
		foreach ($tatArray as $ta){
		    list($tatnr,$vzaby,$vzkd) = explode(':', $ta);
		    array_push($imPalTatArrayToCreate, array('im'=>$ps['im'],'pal'=>$ps['pal'],'stk'=>  intval($ps['stk']),'tat'=>$tatnr,'vzaby'=>  floatval($vzaby),'vzkd'=>  floatval($vzkd)));
		}
	    }
	    
//	    echo "imPalTatArrayToCreate<hr>";
//	    AplDB::varDump($imPalTatArrayToCreate);
	    
	    //ted pojedu po jednotlivych polozkach pole a budu kontrolovat aktualni hodnoty v auftragu
	    //tj, jestli existuje stejna pozice a jake ma hodnoty vzaby a vzkd
	    $previewArray = array();
	    foreach ($imPalTatArrayToCreate as $p){
		$import = $p['im'];
		$pal = $p['pal'];
		$tat = $p['tat'];
		$dauftrArray = $apl->getDauftrPosForImPalTeilTat($import,$pal,$teil,$tat);
		if($dauftrArray!==NULL){
		    //takova pozice uz v auftragu existuje, budu aktualizovat, casy, cenu
		    $dr = $dauftrArray[0];
		    array_push($previewArray, array($p,'id'=>$dr['id'],'vzaby'=>$dr['vzaby'],'vzkd'=>$dr['vzkd'],'action'=>'update'));
		}
		else{
		    //tuto pozici v auftragu nemam, musim ji vytvorit
		    array_push($previewArray, array($p,'id'=>0,'vzaby'=>0,'vzkd'=>0,'action'=>'create'));
		}
	    }

//	    echo "previewArray<hr>";
//	    AplDB::varDump($previewArray);
	    
//	    echo "<pre>";
//	    var_dump($previewArray);
//	    echo "</pre>";
	    
	    $dauftrInserted = 0;
	    $dauftrUpdated = 0;
	    $drueckUpdated = 0;
	    
//	    echo "kunde,im,minPreis,rundenStellen,teil,pal,stk,tatneu,preis,vzkd,vzaby,action<br>";
	    foreach ($previewArray as $pa){
		//import
		$im = $pa[0]['im'];
		$minPreis = $apl->getMinPreisProImport($im);
		$kunde = $apl->getKundeFromAuftransnr($im);
		$rundenStellen = $apl->getKundePreisRundenStellen($kunde);
		$pal = $pa[0]['pal'];
		$stk = $pa[0]['stk'];
		$tatneu = $pa[0]['tat'];
		$vzaby = $pa[0]['vzaby'];
		$vzkd = $pa[0]['vzkd'];
		$preis = round($minPreis*$vzkd,$rundenStellen);
		$action = $pa['action'];
		$vzabyDauftr = $pa['vzaby'];
		$vzkdDauftr = $pa['vzkd'];
		//pridat fremdpos,fremdauftr, atd .... z puvodni G palety
		if($action=="create"){
//		    echo "DAUFTR-CREATE:$kunde,$im,$minPreis,$rundenStellen,$teil,$pal,$stk,$tatneu,$preis,$vzkd,$vzaby,$action<br>";
		    $user = $apl->get_user_pc();
		    if($maTyp=='ima'){
			// pokud vytvarim ze schvalene IMA, nastavim vzdy preis a vzkd na 0
			$preis = 0;
			$vzkd = 0;
		    }
		    $insertDauftrLastId=$apl->insertDauftrRowFromTemplate($im, $teil, $preis, $stk, $pal, $tatneu, $vzkd, $vzaby, $user);
		    $dauftrInserted++;
		}
		if($action=="update"){
		    if($vzaby!=$vzabyDauftr || $vzkd!=$vzkdDauftr){
//			echo "DAUFTR-UPDATE:$kunde,$im,$minPreis,$rundenStellen,$teil,$pal,$stk,$tatneu,$preis,$vzkd,$vzaby,$action<br>";
			$apl->updateDauftrField($pa['id'], 'vzaby', $vzaby);
			if($maTyp!="ima"){
			    // vzkd a preis upravim jen v pripade EMA
			    $apl->updateDauftrField($pa['id'], 'vzkd', $vzkd);
			    $apl->updateDauftrField($pa['id'], 'preis', $preis);
			}
			$dauftrUpdated++;
		    }

		    //test zda mam uvedenou pozici v DRUECK
		    $drueckRows = $apl->getDrueckRowsForImTeilPalTat($im,$teil,$pal,$tatneu);
		    if($drueckRows!==NULL){
//			echo "DRUECK:mam pozice".  count($drueckRows)."<br>";
			foreach ($drueckRows as $dr){
			    $drueckId = $dr['drueck_id'];
			    $vzkdDrueck = $dr['VZ-SOLL'];
			    $vzabyDrueck = $dr['VZ-IST'];
			    if($vzaby!=$vzabyDrueck || $vzkd!=$vzkdDrueck){
//				echo "DRUECK:$drueckId,$vzkdDrueck,$vzabyDrueck<br>";
				if($maTyp!='ima'){
				    $apl->updateDrueckField($drueckId,'VZ-SOLL',$vzkd);
				}
				$apl->updateDrueckField($drueckId,'VZ-IST',$vzaby);
				$drueckUpdated++;
			    }
			}
		    }
		}
	    }
	}
	
//	echo "kontrola dpos pro pozadovane operace<hr>";
	// kontrola dpos pro pozadovane operace
	$dposNew = 0;
	$dposAr = 0;
	foreach ($tatArray as $t){
	    list($tat,$vzaby,$vzkd) = explode(":", $t);
//	    echo "$tat,$vzaby,$vzkd<br>";
	    $dposRows = $apl->getDposInfo($teil,$tat);
	    if($dposRows!==NULL){
		// pro danou operaci mam pozici v dpos, provedu eventuelni update
		// v pripade, ze se casy vzkd nebo vzaby lisi
		foreach ($dposRows as $dpr){
		    $vzabyDpos = $dpr['vzaby'];
		    $vzkdDpos = $dpr['vzkd'];
		    $dposId = $dpr['id'];
		    if($vzaby!=$vzabyDpos || $vzkd!=$vzkdDpos){
			// provedu update
			// jen v pripade EMA
			if($maTyp!='ima'){
			    $ar = $apl->updateDposVZ($dposId,$vzaby,$vzkd);
			    $dposAr = $ar;
			}
//			echo "$dposAr,$dposId,$vzabyDpos,$vzkdDpos<br>";
		    }
		}
	    }
	    else {
		// TODO
		// pro danou operaci jeste nemam zaznam v dpos, musim ho vytvorit
//		echo "pro danou operaci jeste nemam zaznam v dpos, musim ho vytvorit<hr>";
		$insertId = $apl->insertNewDPOS($teil,$tat,$vzaby,$vzkd);
		$dposAr = $insertId;
		$dposNew = 1;
	    }
	}
	
    }
    
    $returnArray = array(
	'maTyp'=>$maTyp,
	'insertDauftrLastId'=>$insertDauftrLastId,
	'imanr'=>$imanr,
	'dposAr'=>$dposAr,
	'dposNew'=>$dposNew,
	'dauftrInserted'=>$dauftrInserted,
	'dauftrUpdated'=>$dauftrUpdated,
	'drueckUpdated'=>$drueckUpdated,
//	'id'=>$id,
//	'imaid'=>$imaid,
//	'imaInfoArray'=>$imaInfoArray,
	'teil'=>$teil,
//	'palArraySkutecne'=>$palArraySkutecne,
//	'imPalTatArrayToCreate'=>$imPalTatArrayToCreate,
//	'previewArray'=>$previewArray,
    );
    
//    echo "<pre>";
//    var_dump($returnArray);
//    echo "</pre>";
    echo json_encode($returnArray);