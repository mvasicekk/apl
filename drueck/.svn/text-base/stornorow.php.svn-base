<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$drueck_id = $_GET['id'];
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	
	// identifikace uzivatele
	$ident=get_user_pc();
	$user = get_user_pc();
	
	$sql = "select * from drueck where (drueck_id='$drueck_id')";
	$result=mysql_query($sql);
	$rowSelect = mysql_fetch_array($result);
	// vytvorim obracene hodnoty ke kusu, ausschussum a verb
	$stornoStk = -$rowSelect['Stück'];
	$stornoAusschuss = -$rowSelect['Auss-Stück'];
	$stornoVerb = -$rowSelect['Verb-Zeit'];
	$auss_typ = $rowSelect['auss_typ'];
	
	// storno i do lagru
	$l_von = lager_von($rowSelect['Teil'], $rowSelect['TaetNr']);
	$l_nach = lager_nach($rowSelect['Teil'], $rowSelect['TaetNr']);
		
	$sql_lager="insert into dlagerbew (";
	$sql_lager.="teil,";
	$sql_lager.="auftrag_import,";
	$sql_lager.="pal_import,";
	$sql_lager.="gut_stk,";
	$sql_lager.="auss_stk,";
	$sql_lager.="lager_von,";
	$sql_lager.="lager_nach,";
	$sql_lager.="comp_user_accessuser,";
	$sql_lager.="abgnr)";
	$sql_lager.=" values(";
	$sql_lager.="'".$rowSelect['Teil']."',";
	$sql_lager.="'".$rowSelect['AuftragsNr']."',";
	$sql_lager.="'".$rowSelect['pos-pal-nr']."',";
	$sql_lager.="'".$stornoStk."',";
	$sql_lager.="'0',";
	$sql_lager.="'".$l_von."',";
	$sql_lager.="'".$l_nach."',";
	$sql_lager.="'".$ident."',";
	$sql_lager.="'".$rowSelect['TaetNr']."')";
	mysql_query($sql_lager);
	$sql_lager_gut=$sql_lager;
	// zmetky do lagru
	if($stornoAusschuss!=0)
	{
		$l_nach = "AX";
		if ($auss_typ == 2) $l_nach = "A2";
		if ($auss_typ == 4) $l_nach = "A4";
		if ($auss_typ == 6) $l_nach = "A6";

		$sql_lager="insert into dlagerbew (";
		$sql_lager.="teil,";
		$sql_lager.="auftrag_import,";
		$sql_lager.="pal_import,";
		$sql_lager.="gut_stk,";
		$sql_lager.="auss_stk,";
		$sql_lager.="lager_von,";
		$sql_lager.="lager_nach,";
		$sql_lager.="comp_user_accessuser,";
		$sql_lager.="abgnr)";
		$sql_lager.=" values(";
		$sql_lager.="'".$rowSelect['Teil']."',";
		$sql_lager.="'".$rowSelect['AuftragsNr']."',";
		$sql_lager.="'".$rowSelect['pos-pal-nr']."',";
		$sql_lager.="'0',";
		$sql_lager.="'".$stornoAusschuss."',";
		$sql_lager.="'".$l_von."',";
		$sql_lager.="'".$l_nach."',";
		$sql_lager.="'".$ident."',";
		$sql_lager.="'".$rowSelect['TaetNr']."')";
		mysql_query($sql_lager);
		$sql_lager_auss=$sql_lager;
	}
	
	
	$sqlInsert = "insert into drueck";
	$sqlInsert.= " (auftragsnr,Teil,TaetNr,Stück,`Auss-Stück`,`VZ-SOLL`,`VZ-IST`,";
	$sqlInsert.= " `Verb-Zeit`,PersNr,Datum,`pos-pal-nr`,`auss-art`,`verb-von`,";
	$sqlInsert.= " `verb-bis`,`verb-pause`,`marke-aufteilung`,schicht,oe,auss_typ,comp_user_accessuser,insert_stamp,kzGut)";
	$sqlInsert.= " values";
	$sqlInsert.= " ('".$rowSelect['AuftragsNr']."','".$rowSelect['Teil']."','".$rowSelect['TaetNr']."','".$stornoStk."','".$stornoAusschuss."','".$rowSelect['VZ-SOLL']."','".$rowSelect['VZ-IST']."',";
	$sqlInsert.= " '".$stornoVerb."','".$rowSelect['PersNr']."','".$rowSelect['Datum']."','".$rowSelect['pos-pal-nr']."','".$rowSelect['auss-art']."','".$rowSelect['verb-von']."',";
	$sqlInsert.= " '".$rowSelect['verb-bis']."','".$rowSelect['verb-pause']."','".$rowSelect['marke-aufteilung']."','".$rowSelect['schicht']."','".$rowSelect['oe']."','".$rowSelect['auss_typ']."','".$user."',NOW(),'".$rowSelect['kzGut']."')";
	mysql_query($sqlInsert);
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
	$output .= $mysql_error;
	$output .= '</mysqlerror>';
	$output .= "<drueck_id>$drueck_id</drueck_id>";
	$output .= "<stornostk>$stornoStk</stornostk>";
	$output .= "<stornoausschuss>$stornoAusschuss</stornoausschuss>";
	$output .= "<stornoverb>$stornoVerb</stornoverb>";
	$output .= "<sqlinsert>$sqlInsert</sqlinsert>";
	$output .= "<sqllagergut>$sql_lager_gut</sqllagergut>";
	$output .= "<sqllagerauss>$sql_lager_auss</sqllagerauss>";
	$output .= '</response>';
	
	echo $output;
	
?>

