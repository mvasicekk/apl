<?
require_once '../db.php';

    $id = $_POST['id'];
    $kundeBoxId = $_POST['kundeBoxId'];
    $imPlanZeit = $_POST['imPlanZeit'];
    $imNr = $_POST['imNr'];
    $bestellNr = $_POST['bestellNr'];
    $bemerkung = $_POST['bemerkung'];
    $termin = $_POST['termin'];
    
    $planTeilStk = $_POST['planTeilStk'];

    $apl = AplDB::getInstance();

    $kunde = substr($id, strrpos($id, '_')+1);
    
    //cena za minutu
    $kdInfoArray = $apl->getKundeInfoArray($kunde);
    $minpreis = floatval($kdInfoArray[0]['preismin']);
    $waehrKz = $kdInfoArray[0]['waehrkz'];
    $sollImDate = substr($kundeBoxId,  strpos($kundeBoxId, '_')+1,10);
    $sollImZeit = $apl->validateZeit($imPlanZeit);
    $aufdatDateTime = date('Y-m-d H:i',  strtotime($sollImDate." ".$sollImZeit));
    $sollImDateTime = date('Y-m-d H:i',  strtotime($sollImDate." ".$sollImZeit));
    
    // vytvorit hlavicku zakazky
    $insertId = $apl->createNewImport($imNr,$kunde,$minpreis,$aufdatDateTime,$sollImDateTime,$waehrKz,$bemerkung,$bestellNr);
    $insertId = 1;
    
    $dauftrInsert = array();
    
    //vytvorit pozice v zakazce, v pripade ze pozaduji nenulovy pocet kusu
    if($planTeilStk>0){
	// fiktivni dil
	$planTeil = $apl->getPlanTeilProKunde($kunde);
	$teilInfo = $apl->getTeilInfoArray($planTeil);
	//pracovni plan
	$dposArray = $apl->getTeilTatArray($planTeil,TRUE);
	if($dposArray!==NULL){
	    $termin = "P".$termin;
	    foreach ($dposArray as $dpos){
		$auftragsnr = $imNr;
		$teil = $planTeil;
		$stk = $planTeilStk;
		$preis = $minpreis*$dpos['vzkd'];
		$kg_stk_bestellung = $teilInfo['Gew'];
		$mehrarbKz = $apl->getRechnungKz($dpos['abgnr']);
		$posPalNr = 0;
		$abgnr = $dpos['abgnr'];
		$kzgut = $dpos['kzgut'];
		$vzkd = $dpos['vzkd'];
		$vzaby = $dpos['vzaby'];
		$comp_user_accessuser = $apl->get_user_pc();
		$inserted = date('Y-m-d H:i:s');
		
		if($kzgut=='G'){
                    $sql = "insert into dauftr (auftragsnr,teil,`Stück`,preis,kg_stk_bestellung,`mehrarb-kz`,`pos-pal-nr`,abgnr,kzgut,";
                    $sql.="vzkd,vzaby,comp_user_accessuser,inserted,termin) values";
                    $sql.="	('$auftragsnr','$teil','$stk','$preis','$kg_stk_bestellung','$mehrarbKz',";
                    $sql.="'$posPalNr','$abgnr','$kzgut','$vzkd','$vzaby','$comp_user_accessuser',NOW(),'$termin')";
		}
		else{
                    $sql = "insert into dauftr (auftragsnr,teil,`Stück`,preis,`mehrarb-kz`,`pos-pal-nr`,abgnr,kzgut,";
                    $sql.="vzkd,vzaby,comp_user_accessuser,inserted,termin) values";
                    $sql.="	('$auftragsnr','$teil','$stk','$preis','$mehrarbKz',";
                    $sql.="'$posPalNr','$abgnr','$kzgut','$vzkd','$vzaby','$comp_user_accessuser',NOW(),'$termin')";
		}
		array_push($dauftrInsert, $sql);
		$apl->query($sql);
	    }
	}
    }
    
    $returnArray = array(
	'id'=>$id,
	'insertId'=>$insertId,
	'kunde'=>$kunde,
	'minpreis'=>$minpreis,
	'waehrKz'=>$waehrKz,
	'sollImDate'=>$sollImDate,
	'sollImZeit'=>$sollImZeit,
	'aufdatDateTime'=>$aufdatDateTime,
	'sollImDateTime'=>$sollImDateTime,
	'kundeBoxId'=>$kundeBoxId,
	'imPlanZeit'=>$imPlanZeit,
	'imNr'=>$imNr,
	'bestellNr'=>$bestellNr,
	'bemerkung'=>$bemerkung,
	'termin'=>$termin,
	'planTeilStk'=>$planTeilStk,
	'dauftrInsert'=>$dauftrInsert,
    );

    
    echo json_encode($returnArray);
?>

