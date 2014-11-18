<?
 session_start();
?>

<?
require_once '../db.php';

    $id=$_POST['id'];
    $imaid = $_POST['imaid'];

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = $apl->get_user_pc();

    //1. vztahnu si informace o schvalene vicepraci
    $imaInfoArray = $apl->getIMAInfoArray($imaid);
    if($imaInfoArray!==NULL){
	$ir = $imaInfoArray[0];
	$teil = $ir['teil'];
	$imArray = explode(';', $ir['ema_auftragsarray_genehmigt']);
	$palArray = explode(';', $ir['ema_palarray_genehmigt']);
	$dauftrIdArray = explode(';', $ir['ema_dauftrid_array_genehmigt']);
	$tatArray = explode(';', $ir['ema_tatundzeitarray_genehmigt']);
	if(is_array($imArray) && is_array($palArray) && is_array($tatArray) && is_array($dauftrIdArray)){
//	    $importePalArray = array();
//	    foreach ($imArray as $import){
//		$palIMArray = $apl->getPaletteMitAuftragTeil('', $import, $teil);
//		foreach ($palIMArray as $palRow){
//		    array_push($importePalArray, array('im'=>$import,'pal'=>$palRow['pal'],'stk'=>$palRow['stk'],'pos'=>$palRow['fremdpos']));
//		}
//	    }
	    
	    $palArraySkutecne = array();
	    foreach ($dauftrIdArray as $i){
		$dauftrRow = $apl->getDauftrRow($i);
		if($dauftrRow!==NULL){
		    array_push($palArraySkutecne, array('im'=>$dauftrRow['auftragsnr'],'pal'=>$dauftrRow['pal'],'stk'=>$dauftrRow['stk'],'pos'=>$dauftrRow['fremdpos']));
		}
	    }
//	    $emaPalArray = $palArray;
//	    foreach ($emaPalArray as $emaPal){
//		// zkusim najit paletu v importech
//		foreach ($importePalArray as $impal){
//		    if($emaPal==$impal['pal']){
//			$imp = $impal['im'];
//			array_push($palArraySkutecne, array('im'=>$impal['im'],'pal'=>$impal['pal'],'stk'=>$impal['stk'],'pos'=>$impal['pos']));
//		    }
//		}
//	    }
	    //vytvorim si pole se skutecnyma pozicema k vytvoreni
	    $imPalTatArrayToCreate = array();
	    foreach ($palArraySkutecne as $ps){
		foreach ($tatArray as $ta){
		    list($tatnr,$vzaby,$vzkd) = explode(':', $ta);
		    array_push($imPalTatArrayToCreate, array('im'=>$ps['im'],'pal'=>$ps['pal'],'stk'=>  intval($ps['stk']),'tat'=>$tatnr,'vzaby'=>  floatval($vzaby),'vzkd'=>  floatval($vzkd)));
		}
	    }
	    
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
		    array_push($previewArray, array($p,'vzaby'=>$dr['vzaby'],'vzkd'=>$dr['vzkd'],'action'=>'update'));
		}
		else{
		    //tuto pozici v auftragu nemam, musim ji vytvorit
		    array_push($previewArray, array($p,'vzaby'=>0,'vzkd'=>0,'action'=>'create'));
		}
	    }
	    
	    //tabulka pro preview
	    $formDiv.="<div id='previewtable'>";
	    $formDiv.="<table>";
	    $formDiv.="<thead>";
	    $formDiv.="<tr>";
	    $formDiv.="<th>importnr</th>";
	    $formDiv.="<th>teil</th>";
	    $formDiv.="<th>pal</th>";
	    $formDiv.="<th>stk</th>";
	    $formDiv.="<th>abgnr-ist</th>";
	    $formDiv.="<th>vzaby-ist</th>";
	    $formDiv.="<th>vzkd-ist</th>";
	    $formDiv.="<th>abgnr-NEU</th>";
	    $formDiv.="<th>vzaby-NEU</th>";
	    $formDiv.="<th>vzkd-NEU</th>";
	    $formDiv.="<th>action</th>";
	    $formDiv.="</tr>";
	    $formDiv.="</thead>";
	    $formDiv.="<tbody>";
	    $i=0;
	    foreach ($previewArray as $pa){
		$class=$i++%2?'sudy':'lichy';
		$formDiv.="<tr class='$class'>";
		//import
		$formDiv.="<td>";
		$formDiv.=$pa[0]['im'];
		$formDiv.="</td>";
		//teil
		$formDiv.="<td>";
		$formDiv.=$teil;
		$formDiv.="</td>";
		
		//pal
		$formDiv.="<td>";
		$formDiv.=$pa[0]['pal'];
		$formDiv.="</td>";

		//stk
		$formDiv.="<td>";
		$formDiv.=$pa[0]['stk'];
		$formDiv.="</td>";
		
		//abgnr-ist
		$obsah = $pa['action']=='create'?'':$pa[0]['tat'];
		$formDiv.="<td>";
		$formDiv.=$obsah;
		$formDiv.="</td>";

		//vzaby-ist
		$obsah = $pa['action']=='create'?'':$pa['vzaby'];
		$formDiv.="<td>";
		$formDiv.=$obsah;
		$formDiv.="</td>";

		//vzkd-ist
		$obsah = $pa['action']=='create'?'':$pa['vzkd'];
		$formDiv.="<td>";
		$formDiv.=$obsah;
		$formDiv.="</td>";

		//abgnr-NEU
		$formDiv.="<td>";
		$formDiv.=$pa[0]['tat'];
		$formDiv.="</td>";

		//vzaby-NEU
		$formDiv.="<td>";
		$formDiv.=$pa[0]['vzaby'];
		$formDiv.="</td>";
		
		//vzkd-NEU
		$formDiv.="<td>";
		$formDiv.=$pa[0]['vzkd'];
		$formDiv.="</td>";

		//action
		$formDiv.="<td>";
		$formDiv.=$pa['action'];
		$formDiv.="</td>";

		$formDiv.="</tr>";
		
	    }
	    $formDiv.="<tr>";
	    $formDiv.="<td style='text-align:center;' colspan='11'>";
	    $formDiv.="<input type='button' id='createDauftrPositionen' acturl='createDauftrDMAPositionen.php' value='Positionen erstellen !!!'/>";
	    $formDiv.="</td>";
	    $formDiv.="</tr>";

	    $formDiv.="</tbody>";
	    $formDiv.="</table>";
	    $formDiv.="</div>";
	}
    }
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'imaid'=>$imaid,
	'imaInfoArray'=>$imaInfoArray,
	'teil'=>$teil,
	'palArraySkutecne'=>$palArraySkutecne,
	'imPalTatArrayToCreate'=>$imPalTatArrayToCreate,
	'previewArray'=>$previewArray,
	'formDiv'=>$formDiv,
    );
    echo json_encode($returnArray);

?>

