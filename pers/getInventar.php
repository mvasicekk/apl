<?
require_once '../db.php';

    $e = $_GET['e'];
    
    

    $apl = AplDB::getInstance();

    // budu vracet jen inventar, ktery nema nikdo prirazen
    $inventarArray = NULL;
    if(strlen($e)>=1){
	$sql.=" select ";
	$sql.=" inventartyp.typ as inventartyp,";
	$sql.=" inventartyp.popis as inventartyp_popis,";
	$sql.=" inventar.*,";
	$sql.=" mistnosti.mistnost,";
	$sql.=" mistnosti.popis as popis_mistnosti";
	$sql.=" from";
	$sql.=" inventar";
	$sql.=" left join dpersinventar on dpersinventar.inventar_id=inventar.id";
	$sql.=" left join inventartyp on inventartyp.id=inventar.typinventare_id";
	$sql.=" left join mistnosti on mistnosti.id=inventar.mistnost_id";
	$sql.=" where";
	$sql.=" (dpersinventar.id is null or dpersinventar.vraceno_datum is not null)";	    // nikdo ho nema
	$sql.=" and (";
	$sql.=" inventar.popis like LOWER('%$e%')";
	$sql.=" or CONVERT(inventar.cislo,CHAR) like LOWER('%$e%')";
	$sql.=" or inventartyp.typ like LOWER('%$e%')";
	$sql.=" or mistnosti.mistnost like LOWER('%$e%')";
	$sql.=" or mistnosti.popis like LOWER('%$e%')";
	$sql.=" )";				    // konec and
	$sql.=" order by";
	$sql.=" inventartyp.typ,";
	$sql.=" inventar.cislo";
	$iA = $apl->getQueryRows($sql);
    }
    
    if($iA!==NULL){
	$inventarArray = $iA;
	foreach ($iA as $i=>$row){
	    //zjistim zda polozka nema nejake potomky
	    $rodicId = $row['id'];
	    $sql=" select ";
	    $sql.=" inventartyp.typ as inventartyp,";
	    $sql.=" inventartyp.popis as inventartyp_popis,";
	    $sql.=" inventar.*";
	    $sql.=" from";
	    $sql.=" inventar";
	    $sql.=" left join inventartyp on inventartyp.id=inventar.typinventare_id";
	    $sql.=" where";
	    $sql.=" inventar.parent_id='$rodicId'";
	    $potomciRows = $apl->getQueryRows($sql);
	    if($potomciRows!==NULL){
		$inventarArray[$i]['pocetpotomku'] = count($potomciRows);
		$potomek1 = sprintf("(přiřazeno k: %s - %s %s)",$potomciRows[0]['inventartyp'],$potomciRows[0]['cislo'],$potomciRows[0]['popis']);
		$potomek = sprintf("%s %s",$potomciRows[0]['cislo'],$potomciRows[0]['popis']);
	    }
	    else{
		$inventarArray[$i]['pocetpotomku'] = 0;
		$potomek1="";
		$potomek = "";
	    }
	    
	    //zjistim zda polozka nema nejake rodice
	    $potomekId = $row['parent_id'];
	    $sql=" select ";
	    $sql.=" inventartyp.typ as inventartyp,";
	    $sql.=" inventartyp.popis as inventartyp_popis,";
	    $sql.=" inventar.*";
	    $sql.=" from";
	    $sql.=" inventar";
	    $sql.=" left join inventartyp on inventartyp.id=inventar.typinventare_id";
	    $sql.=" where";
	    $sql.=" inventar.id='$potomekId'";
	    $rodiceRows = $apl->getQueryRows($sql);
	    if($rodiceRows!==NULL){
		$inventarArray[$i]['pocetrodicu'] = count($rodiceRows);
		$rodic1 = sprintf("(přiřazeno k: %s - %s %s)",$rodiceRows[0]['inventartyp'],$rodiceRows[0]['cislo'],$rodiceRows[0]['popis']);
		$rodic = sprintf("%s %s",$rodiceRows[0]['cislo'],$rodiceRows[0]['popis']);
	    }
	    else{
		$inventarArray[$i]['pocetrodicu'] = 0;
		$rodic1="";
		$rodic = "";
	    }
	    
	    //$inventarArray[$i]['formattedInventar'] = sprintf("<div>%s - %s %s (místnost: %s) $potomek1 $rodic1</div>",$row['inventartyp'],$row['cislo'],$row['popis'],$row['mistnost']);
	    $inventarArray[$i]['formattedInventar'] = sprintf("<div>%s - %s %s (místnost: %s)</div>",$row['inventartyp'],$row['cislo'],$row['popis'],$row['mistnost']);
	    $inventarArray[$i]['potomek'] = $potomek;
	    $inventarArray[$i]['rodic'] = $rodic;
	    $inventarArray[$i]['vydej_datum1'] = strtotime($inventarArray[$i]['vydej_datum']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'inventarArray'=>$inventarArray,
	'sql'=>$sql
    );
    echo json_encode($returnArray);

