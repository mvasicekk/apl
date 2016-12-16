<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$termin = $o->termin;
$teilsuchen = strtolower(trim($o->teil));
$kunde = $o->kunde;
$teile = NULL;

$a = AplDB::getInstance();
		
if($termin!==0){
    // vybrat terminovane radky
    $termin = date('Y-m-d',  strtotime($termin));
    $sql.=" select dauftr.auftragsnr,";
    $sql.=" daufkopf.kunde,";
    $sql.=" dauftr.`pos-pal-nr` as pal,";
$sql.=" dauftr.teil,";
$sql.=" dauftr.`stück` as im_stk,";
$sql.=" dauftr.`mehrarb-kz` as tat_kz,";
$sql.=" dauftr.abgnr,";
$sql.=" dauftr.VzAby as vzaby,";
$sql.=" dauftr.termin,";
$sql.=" sum(if(drueck.auss_typ=2,drueck.`Auss-Stück`,0)) as a2,";
$sql.=" sum(if(drueck.auss_typ=4,drueck.`Auss-Stück`,0)) as a4,";
$sql.=" sum(if(drueck.auss_typ=6,drueck.`Auss-Stück`,0)) as a6,";
$sql.=" sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) as gut_stk";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
$sql.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sql.=" where";
//$sql.="     LOWER(dauftr.teil) like '%$teilsuchen%'";
//$sql.="     daufkopf.kunde='$kunde'";
//$sql.="     and (dauftr.abgnr between 1100 and 1299)";
$sql.="     (`dtaetkz-abg`.stat_nr='S0061' or `dtaetkz-abg`.stat_nr='S0062')";
$sql.="     and dauftr.`auftragsnr-exp` is null";
$sql.="     and dauftr.teil not like '%IM'";
$sql.="     and dauftr.f_tablo_termin='$termin'";
$sql.=" group by";
$sql.=" daufkopf.kunde,";
$sql.=" dauftr.auftragsnr,";
$sql.=" dauftr.teil,";
$sql.=" dauftr.`pos-pal-nr`,";
$sql.=" dauftr.abgnr    ";
$sql.=" order by";
$sql.="     dauftr.f_tablo_order,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";

}
 else {
     if(strlen(trim($teilsuchen))>=3){
	 // vybrat radky bez terminu
	 // jak vybrat palety, ktere obsahuji alespon jednu barevnou operaci ?
    $sql.=" select dauftr.auftragsnr,";
    $sql.=" dauftr.`pos-pal-nr` as pal,";
    $sql.=" daufkopf.kunde,";
    $sql.=" dauftr.teil,";
    $sql.=" dauftr.`stück` as im_stk,";
    $sql.=" dauftr.`mehrarb-kz` as tat_kz,";
    $sql.=" dauftr.abgnr,";
    $sql.=" dauftr.VzAby as vzaby,";
    $sql.=" dauftr.termin,";
    $sql.=" `dtaetkz-abg`.stat_nr as statnr,";
    $sql.=" sum(if(drueck.auss_typ=2,drueck.`Auss-Stück`,0)) as a2,";
    $sql.=" sum(if(drueck.auss_typ=4,drueck.`Auss-Stück`,0)) as a4,";
    $sql.=" sum(if(drueck.auss_typ=6,drueck.`Auss-Stück`,0)) as a6,";
    $sql.=" sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) as gut_stk";
    $sql.=" from dauftr";
    $sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
    $sql.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
    $sql.=" where";
    $sql.="     LOWER(dauftr.teil) like '%$teilsuchen%'";
    //$sql.="     and daufkopf.kunde='$kunde'";
    //$sql.="     and (dauftr.abgnr between 1100 and 1299)";
    //$sql.="     and (`dtaetkz-abg`.stat_nr='S0061' or `dtaetkz-abg`.stat_nr='S0062')";
    $sql.="     and dauftr.`auftragsnr-exp` is null";
    $sql.="     and dauftr.teil not like '%IM'";
    $sql.="     and dauftr.f_tablo_termin is null";
    $sql.=" group by";
    $sql.=" daufkopf.kunde,";
    $sql.=" dauftr.auftragsnr,";
    $sql.=" dauftr.teil,";
    $sql.=" dauftr.`pos-pal-nr`,";
    $sql.=" dauftr.abgnr    ";
    $sql.=" order by";
    $sql.="     daufkopf.kunde,";
    $sql.="     dauftr.teil,";
    $sql.="     dauftr.auftragsnr,";
    $sql.="     dauftr.`pos-pal-nr`,";
    $sql.="     dauftr.abgnr";
     }
    
}

if(strlen($sql)>0){
    $teile = $a->getQueryRows($sql);
}


$returnArray = array(
	'teile'=>$teile,
	'teilsuchen'=>$teilsuchen,
	'sql'=>$sql
    );
    
echo json_encode($returnArray);
