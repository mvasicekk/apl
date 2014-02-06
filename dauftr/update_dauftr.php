<?
session_start();
require "../fns_dotazy.php";
require_once '../db.php';
dbConnect();

$aplDB = AplDB::getInstance();
$a = $aplDB;

	// TODO: dodelat validaci parametru
	
	$dauftr_id = $_GET['id'];
	
	$teil=trim($_GET['teil']);
	$dil = $teil;
	
	$pos_pal_nr=chop($_GET['pos_pal_nr']);
	$stk=chop($_GET['stk']);
	$preis=chop($_GET['preis']);
	$mehrarb_kz=chop($_GET['mehrarb_kz']);
	$abgnr=chop($_GET['abgnr']);
	$KzGut=chop($_GET['KzGut']);
	$termin=chop($_GET['termin']);
	$auftragsnr_exp=chop($_GET['auftragsnr_exp']);
	$auftragsnr=chop($_GET['auftragsnr']);

	// pokud neni auftragsnr_exp cislo nastavim ho na null
	if(strlen($auftragsnr_exp)==0)
		$auftragsnr_exp='NULL';
		
	// pokud neni pos_pal_nr_exp cislo nastavim ho na null	
	$pos_pal_nr_exp=chop($_GET['pos_pal_nr_exp']);
	if(strlen($pos_pal_nr_exp)==0)
		$pos_pal_nr_exp='NULL';
	
	
	
	$stk_exp=chop($_GET['stk_exp']);
	// pokud neni stk_exp cislo nastavim ho na null
	if(strlen($stk_exp)==0)
		$stk_exp='NULL';
	
	$fremdauftr=chop($_GET['fremdauftr']);
	$fremdpos=chop($_GET['fremdpos']);
	
	$KzGut=chop($_GET['KzGut']);
	//cokoliv jineho nez mezeru nahradim pismenem G
	if(strlen($KzGut)>0)
		$KzGut='G';

	$dauftrRow = $a->getDauftrRow($dauftr_id);
	$auftragsnrExpDB = $dauftrRow['ex'];
	$expStkDB = $dauftrRow['ex_stk'];
	
	//foreach($_GET as $prvek=>$klic)
	//	echo "$prvek = $klic<br>";
		
	// pokud uzivatel zmenil preis musim zmenit odpovidajicim zpusobem i vzkd pro danou pozici
	// vzkd = preis / minpreis
	$vzkd_neu = round(getVzKdFromPreisAuftrag($preis,$auftragsnr_exp),4);
	
	// potichu zmenim i hodnoty vzkd v druecku pro danou paletu a auftrag
	// TODO mam to delat automaticky ?
	updateDrueckVzKdFromAuftrag($vzkd_neu,$pos_pal_nr,$teil,$abgnr,$auftragsnr);
	
	$pocitac=$_SERVER["REMOTE_ADDR"];
	$ident = get_user_pc();
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////

	mysql_query("set names utf8");

	// $termin rozkopirovat do vsech pozic pro danou paletu
	// $auftragsnr_exp
	// $pos_pal_nr_exp
	// $fremdauftr
	// $fremdpos
	// podle dauftr_id si zjistim auftrag atd

	$invDatum = "";
    
	if ($KzGut == 'G') {
	    $myerror = updateDauftr_Termin_AuftragsnrExp_PalExp_fremdauftr_fremdpos($stk, $termin, $auftragsnr_exp, $pos_pal_nr_exp, $fremdauftr, $fremdpos, $dauftr_id);
	    // zjistitit, zda uz dil nahodou nemel inventuru
	    $invDatum = $aplDB->getInventurDatumForTeil($aplDB->getTeilFromDauftrId($dauftr_id));
	    $dauftrStampRow = $aplDB->getRowFromDauftrId($dauftr_id);
	    $dauftrStamp = $dauftrStampRow['stamp1'];
	    $invtime = strtotime($invDatum);
	    $dauftrtime = strtotime($dauftrStamp);
	    if ($invtime > $dauftrtime)
		$timeBeachten = 1;
	    else
		$timeBeachten = 0;
	    
	    // 2014-02-05
	    // podle puvodniho obsahu musim rozhodnout, co udelat s polozkami v dlagerbew
	    $strlenExDB = strlen(trim($auftragsnrExpDB));
	    $strlenEx = strlen(trim($auftragsnr_exp));
	    // 1, ex geloescht -> storno v dlagerbew
	    $storno=0;
	    if(($strlenExDB>0) && ($auftragsnr_exp=='NULL')){
		$storno=1;
		$a->stornoLastDlagerBewExport($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	    }
	    else if(($strlenExDB==0)&&($strlenEx)>0){
		// 2, vyplnen prazdny export, pohyb v dlagerbew jako u export fullen
		$gut = intval($stk_exp);
		$a->insertDlagerBew($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $gut, 0, "8E", "8X", $ident);
		// presun do dummy lagru, aby mi nezbyvalo v prvnim skladu
		$a->insertDlagerBewXXDummy($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $ident);
		// presun zmetku ve vyrobe do zmetku vyexportovanych, pocty si beruz tabulky drueck
		$a->moveAussLagerA2B($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	    }
	    else if(($strlenExDB>0)&&($strlenEx>0)&&(intval($stk_exp)!=intval($expStkDB))){
		// 3, zmena poctu kusu -> storno + export fullen
		// storno
		$a->stornoLastDlagerBewExport($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
		//insert
		$gut = intval($stk_exp);
		$a->insertDlagerBew($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $gut, 0, "8E", "8X", $ident);
		// presun do dummy lagru, aby mi nezbyvalo v prvnim skladu
		$a->insertDlagerBewXXDummy($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $ident);
		// presun zmetku ve vyrobe do zmetku vyexportovanych, pocty si beruz tabulky drueck
		$a->moveAussLagerA2B($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	    }
	    
    }



$sql="update dauftr";
	$sql.=" set ";
	if(strlen($teil)>0)
		$sql.="`Teil`='".$teil."',";
    $sql.=" `Stück`='".$stk."',";
	$sql.=" `Termin`='".$termin."',";
	if(strlen($preis)>0)
		$sql.=" `Preis`='".$preis."',";
	if(strlen($mehrarb_kz)>0)
		$sql.=" `MehrArb-KZ`='".$mehrarb_kz."',";
	if(strlen($pos_pal_nr)>0)
		$sql.=" `pos-pal-nr`='".$pos_pal_nr."',";
	$sql.=" `auftragsnr-exp`=".$auftragsnr_exp.",";
	$sql.=" `pal-nr-exp`=".$pos_pal_nr_exp.",";
	$sql.=" `stk-exp`=".$stk_exp.",";
	$sql.=" `fremdauftr`='".$fremdauftr."',";
	$sql.=" `fremdpos`='".$fremdpos."',";
	$sql.=" `KzGut`='".$KzGut."',";
	if(strlen($abgnr)>0)
		$sql.=" `abgnr`='".$abgnr."',";
	//$sql.=" `vzkd`='".$vzkd_neu."',";
	//$sql.=" `VzAby`='".$teil."',";
	$sql.=" `comp_user_accessuser`='".$ident."'";
	$sql.=" where (id_dauftr=".$dauftr_id.") limit 1";
	
	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	//$output .= $mysql_error;
	$output .= $myerror;
	$output .= '</mysqlerror>';

    $output .="<invdatum>$invDatum</invdatum>";
    $output .="<exDB>$auftragsnrExpDB</exDB>";
    $output .="<strlenex>$strlenExDB-$strlenEx</strlenex>";
    $output .="<storno>$storno</storno>";
    $output .="<dauftrstamp>$dauftrStamp</dauftrstamp>";
    $output .="<timebeachten>$timeBeachten</timebeachten>";
	$output .= '</response>';

    
	echo $output;
	
	
	
?>

