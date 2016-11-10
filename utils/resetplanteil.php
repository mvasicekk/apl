<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../db.php';
$a = AplDB::getInstance();
$apl = $a;

$sql=" select dauftr.termin,daufkopf.kunde,daufkopf.auftragsnr,dauftr.`stück` as imstk ";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" where";
$sql.=" teil like '99%IM'";
//$sql.=" and daufkopf.kunde<355 and KzGut='G'";
$sql.=" group by";
$sql.=" daufkopf.kunde,daufkopf.auftragsnr,dauftr.termin";

$imRows = $a->getQueryRows($sql);
$dauftrInsert = array();

//smazat pozice

$sql=" delete ";
$sql.=" from dauftr";
//$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" where";
$sql.=" teil like '99%IM'";
//$sql.=" and daufkopf.kunde<355";

echo "delete query: $sql<hr>";
$a->query($sql);

// vytvorit nove

foreach ($imRows as $ir){
    $planTeilStk = $ir['imstk'];
    $kunde = $ir['kunde'];
    $termin = $ir['termin'];
    $imNr = $ir['auftragsnr'];
    $kdInfoArray = $apl->getKundeInfoArray($kunde);
    $minpreis = floatval($kdInfoArray[0]['preismin']);
    
    if($planTeilStk>0){
	// fiktivni dil
	$planTeil = $apl->getPlanTeilProKunde($kunde);
	$teilInfo = $apl->getTeilInfoArray($planTeil);
	//pracovni plan
	$dposArray = $apl->getTeilTatArray($planTeil,TRUE);
	if($dposArray!==NULL){
	    //$termin = "P".$termin;
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
		$comp_user_accessuser = "jr_resetplanteil";
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
}

AplDB::varDump($dauftrInsert);

