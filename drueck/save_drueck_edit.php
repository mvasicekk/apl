<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	mysql_query('set names utf8');
	
	// TODO: dodelat validaci parametru

	$auftragsnr=trim($_GET['auftragsnr']);
	$datum=trim($_GET['datum']);
	$pal=trim($_GET['pal']);
	$teil=trim($_GET['teil']);
	$mehr=trim($_GET['mehr']);
	$tat1=trim($_GET['tat1']);
	$tat2=trim($_GET['tat2']);
	$tat3=trim($_GET['tat3']);
	$tat4=trim($_GET['tat4']);
	$tat5=trim($_GET['tat5']);
	$tat6=trim($_GET['tat6']);
	$tat1_abymin=trim($_GET['tat1_abymin']);
	$tat2_abymin=trim($_GET['tat2_abymin']);
	$tat3_abymin=trim($_GET['tat3_abymin']);
	$tat4_abymin=trim($_GET['tat4_abymin']);
	$tat5_abymin=trim($_GET['tat5_abymin']);
	$tat6_abymin=trim($_GET['tat6_abymin']);
	$tat1_kdmin=trim($_GET['tat1_kdmin']);
	$tat2_kdmin=trim($_GET['tat2_kdmin']);
	$tat3_kdmin=trim($_GET['tat3_kdmin']);
	$tat4_kdmin=trim($_GET['tat4_kdmin']);
	$tat5_kdmin=trim($_GET['tat5_kdmin']);
	$tat6_kdmin=trim($_GET['tat6_kdmin']);
	$persnr=trim($_GET['persnr']);
	$schicht=trim($_GET['schicht']);
        $oe=trim($_GET['oe']);
	$stk=trim($_GET['stk']);
	$auss_stk=trim($_GET['auss_stk']);
	$auss_art=trim($_GET['auss_art']);
	$auss_typ=trim($_GET['auss_typ']);
	$von=trim($_GET['von']);
	$bis=trim($_GET['bis']);
	$pause=trim($_GET['pause']);
	$verb=trim($_GET['verb']);
	$vzaby_pro_stk=trim($_GET['vzaby_pro_stk']);
	$stornoid = trim($_GET['stornoid']);
	$stornoidarray = urldecode($_GET['stornoidarray']);
	
	$stornoArray = explode(':',$stornoidarray);

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	
	// nejdriv zjistim kolik mam nenulovych hodnot pro tatX a vytvorim si z techto nenulovych hodnot pole i 
	// s casama vzaby

	$tat_array = array();
	$vz_array = array();
	$vzkd_array = array();
	
	if($tat1!=0)
	{
		array_push($tat_array,$tat1);
		array_push($vz_array,$tat1_abymin);
		array_push($vzkd_array,$tat1_kdmin);
	}


	if($tat2!=0)
	{
		array_push($tat_array,$tat2);
		array_push($vz_array,$tat2_abymin);
		array_push($vzkd_array,$tat2_kdmin);
	}
	if($tat3!=0)
	{
		array_push($tat_array,$tat3);
		array_push($vz_array,$tat3_abymin);
		array_push($vzkd_array,$tat3_kdmin);
	}
	if($tat4!=0)
	{
		array_push($tat_array,$tat4);
		array_push($vz_array,$tat4_abymin);
		array_push($vzkd_array,$tat4_kdmin);
	}
	if($tat5!=0)
	{
		array_push($tat_array,$tat5);
		array_push($vz_array,$tat5_abymin);
		array_push($vzkd_array,$tat5_kdmin);
	}
	if($tat6!=0)
	{
		array_push($tat_array,$tat6);
		array_push($vz_array,$tat6_abymin);
		array_push($vzkd_array,$tat6_kdmin);
	}

	// podle poctu prvku v poli tat_array vytvorim potrebne mnozstvi insertu
	
	$insert_array=array();
	$lager_insert_array=array();
	
	// identifikace uzivatele
	$ident=get_user_pc();

	// cast tykajici se rozpocitani spotrebovaneho casu
	if($mehr==0)
	{
		for($i=0;$i<sizeof($tat_array);$i++)
		{
			if($vzaby_pro_stk!=0)
				$cast_verb = round($vz_array[$i]/$vzaby_pro_stk*$verb);
			else
				$cast_verb =0;
			
			if(($i==0)&&(sizeof($tat_array)>1))
				$marke_aufteilung='A';
			else
				$marke_aufteilung='';
			
			// 	pokud mam zmetky, tak je dam pouze k prvni operaci
			if($i==0)
			{
				// 	nedelam nic
			}
			else
			{
				// 	vynuluju auss_stk,auss_art,auss_typ
				$auss_stk=0;
				$auss_art=0;
				$auss_typ=0;
                                //pauzu jen u prvniho zaznamu, pri vice operacich uz zapsat jen nulu
                                $pause=0;
			}
		
		
		
			//udelam zapis do tabulky DLagerbew
			//		zjistim nazvy lagru
			//	kolik a jake operace jsou zadany
			//1.operace, ta je tam vzdy
			$l_von = lager_von($teil, $tat_array[$i]);
			$l_nach = lager_nach($teil, $tat_array[$i]);
		
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
			$sql_lager.="'".$teil."',";
			$sql_lager.="'".$auftragsnr."',";
			$sql_lager.="'".$pal."',";
			$sql_lager.="'".$stk."',";
			$sql_lager.="0,";
			$sql_lager.="'".$l_von."',";
			$sql_lager.="'".$l_nach."',";
			$sql_lager.="'".$ident."',";
			$sql_lager.="'".$tat_array[$i]."')";
			
			array_push($lager_insert_array,$sql_lager);

			if($auss_stk!=0)
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
				$sql_lager.="'".$teil."',";
				$sql_lager.="'".$auftragsnr."',";
				$sql_lager.="'".$pal."',";
				$sql_lager.="'0',";
				$sql_lager.="'".$auss_stk."',";
				$sql_lager.="'".$l_von."',";
				$sql_lager.="'".$l_nach."',";
				$sql_lager.="'".$ident."',";
				$sql_lager.="'".$tat_array[$i]."')";
			
				array_push($lager_insert_array,$sql_lager);
			}
		
			$sql="insert into drueck (";
			$sql.="AuftragsNr,";
			$sql.="Teil,";
			$sql.="TaetNr,";
			$sql.="`Stück`,";
			$sql.="`Auss-Stück`,";
			$sql.="`VZ-SOLL`,";
			$sql.="`VZ-IST`,";
			$sql.="`Verb-Zeit`,";
			$sql.="PersNr,";
			$sql.="Datum,";
			$sql.="`pos-pal-nr`,";
			$sql.="`auss-art`,";
			$sql.="`verb-von`,";
			$sql.="`verb-bis`,";
			$sql.="`verb-pause`,";
			$sql.="`marke-aufteilung`,";
			$sql.="schicht,";
                        $sql.="oe,";
			$sql.="auss_typ,";
			$sql.="comp_user_accessuser,";
			$sql.="insert_stamp,";
			$sql.="kzGut)";
			$sql.="values(";
			$sql.="'".$auftragsnr."',";
			$sql.="'".$teil."',";
			$sql.="'".$tat_array[$i]."',";
			$sql.="'".$stk."',";
			$sql.="'".$auss_stk."',";
			$sql.="'".$vzkd_array[$i]."',";
			$sql.="'".$vz_array[$i]."',";
			$sql.="'".$cast_verb."',";
			$sql.="'".$persnr."',";
			$sql.="'".make_DB_datum($datum)."',";
			$sql.="'".$pal."',";
			$sql.="'".$auss_art."',";
			$sql.="'".make_DB_datetime($von,$datum)."',";
			$sql.="'".make_DB_datetime($bis,$datum)."',";
			$sql.="'".$pause."',";
			$sql.="'".$marke_aufteilung."',";
			$sql.="'".$schicht."',";
                        $sql.="'".$oe."',";
			$sql.="'".$auss_typ."',";
			$sql.="'".$ident."',";
			$sql.="NOW(),";
			$sql.="' '";
			$sql.=")";
			array_push($insert_array,$sql);
		}
	}
	else
	{
		$cast_verb=$verb;
		$sql="insert into drueck (";
		$sql.="AuftragsNr,";
		$sql.="Teil,";
		$sql.="TaetNr,";
		$sql.="`Stück`,";
		$sql.="`Auss-Stück`,";
		$sql.="`VZ-SOLL`,";
		$sql.="`VZ-IST`,";
		$sql.="`Verb-Zeit`,";
		$sql.="PersNr,";
		$sql.="Datum,";
		$sql.="`pos-pal-nr`,";
		$sql.="`auss-art`,";
		$sql.="`verb-von`,";
		$sql.="`verb-bis`,";
		$sql.="`verb-pause`,";
		$sql.="`marke-aufteilung`,";
		$sql.="schicht,";
                $sql.="oe,";
		$sql.="auss_typ,";
		$sql.="comp_user_accessuser,";
		$sql.="insert_stamp,";
		$sql.="kzGut)";
		$sql.="values(";
		$sql.="'".$auftragsnr."',";
		$sql.="'".$teil."',";
		$sql.="'".$tat_array[0]."',";
		$sql.="'".$stk."',";
		$sql.="'".$auss_stk."',";
		$sql.="'".$vzkd_array[0]."',";
		$sql.="'".$vzaby_pro_stk."',";
		$sql.="'".$cast_verb."',";
		$sql.="'".$persnr."',";
		$sql.="'".make_DB_datum($datum)."',";
		$sql.="'".$pal."',";
		$sql.="'".$auss_art."',";
		$sql.="'".make_DB_datetime($von,$datum)."',";
		$sql.="'".make_DB_datetime($bis,$datum)."',";
		$sql.="'".$pause."',";
		$sql.="'".$marke_aufteilung."',";
		$sql.="'".$schicht."',";
                $sql.="'".$oe."',";
		$sql.="'".$auss_typ."',";
		$sql.="'".$ident."',";
		$sql.="NOW(),";
		$sql.="' '";
		$sql.=")";
		array_push($insert_array,$sql);
		
		//udelam zapis do tabulky DLagerbew
		//zjistim nazvy lagru
		//kolik a jake operace jsou zadany
		//1.operace, ta je tam vzdy
		$l_von = lager_von($teil, $tat_array[0]);
		$l_nach = lager_nach($teil, $tat_array[0]);
		
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
		$sql_lager.="'".$teil."',";
		$sql_lager.="'".$auftragsnr."',";
		$sql_lager.="'".$pal."',";
		$sql_lager.="'".$stk."',";
		$sql_lager.="'0',";
		$sql_lager.="'".$l_von."',";
		$sql_lager.="'".$l_nach."',";
		$sql_lager.="'".$ident."',";
		$sql_lager.="'".$tat_array[0]."')";
		
		array_push($lager_insert_array,$sql_lager);

		if($auss_stk!=0)
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
			$sql_lager.="'".$teil."',";
			$sql_lager.="'".$auftragsnr."',";
			$sql_lager.="'".$pal."',";
			$sql_lager.="'0',";
			$sql_lager.="'".$auss_stk."',";
			$sql_lager.="'".$l_von."',";
			$sql_lager.="'".$l_nach."',";
			$sql_lager.="'".$ident."',";
			$sql_lager.="'".$tat_array[0]."')";
		
			array_push($lager_insert_array,$sql_lager);
		}

	}

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';

	// provedu serii insertu do druecku
	$output .= '<serieinsertu>';
	for($i=0;$i<sizeof($insert_array);$i++)
	{
		$sql=$insert_array[$i];
		$result=mysql_query($sql);
		$affected_rows=mysql_affected_rows();
		$mysqlerror=mysql_error();
		$output .= '<sqlinsert>';
		$output .= $sql;
		$output .= '</sqlinsert>';
		$output .= '<affected_rows>';
		$output .= $affected_rows;
		$output .= '</affected_rows>';
		$output .= '<mysqlerror>';
		$output .= $mysqlerror;
		$output .= '</mysqlerror>';
	}
	$output .= '</serieinsertu>';
	

	// provedu serii insertu do dlagerbew
	$output .= '<lager_serieinsertu>';
	for($i=0;$i<sizeof($lager_insert_array);$i++)
	{
		$sql=$lager_insert_array[$i];
		$result=mysql_query($sql);
		$affected_rows=mysql_affected_rows();
		$mysqlerror=mysql_error();
		$output .= '<lager_sqlinsert>';
		$output .= $sql;
		$output .= '</lager_sqlinsert>';
		$output .= '<affected_rows>';
		$output .= $affected_rows;
		$output .= '</affected_rows>';
		$output .= '<mysqlerror>';
		$output .= $mysqlerror;
		$output .= '</mysqlerror>';
	}
	$output .= '</lager_serieinsertu>';

	
	// provedu storno podle obsahu pole $stornoArray
	foreach($stornoArray as $stornoid)
	{
		// provedu storno polozky podle hodnotu stornoid
		mysql_query('set names utf8');
		$sql = "select * from drueck where (drueck_id='$stornoid')";
		$result=mysql_query($sql);
		$user = get_user_pc();
		$rowSelect = mysql_fetch_array($result);
		// 	vytvorim obracene hodnoty ke kusu, ausschussum a verb
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
		
		// 	zmetky do lagru
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
	
		$output .= "<stornoid>$stornoid</stornoid>";
	}
	
	$output .= '</response>';
	
	echo $output;
	
?>