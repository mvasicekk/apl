<?
session_start();
require "../../fns_dotazy.php";
require_once '../../db.php';
dbConnect();


	// TODO: dodelat validaci parametru
	
	$list = trim($_GET['list']);
	$export = trim($_GET['export']);
	$import = trim($_GET['import']);

	$listArray = explode(',',$list);


 
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	//otestovat, jestli zadany export existuje a neni uz nahodou vyfakturovany

	$a = AplDB::getInstance();
	mysql_query('set names utf8');
	
	$ident = get_user_pc();
	
	$sql="select auftragsnr from daufkopf where ((auftragsnr='$export') and (fertig='2100-01-01'))";
	$ret=mysql_query($sql);
	$pocet_vysledku=mysql_affected_rows();


	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<export>'.$export.'</export>';

	if($pocet_vysledku>0)
	{

		foreach($listArray as $idArray)
		{
			$idGutAussArray = explode(':',$idArray);
			list($id,$gut,$auss2,$auss4,$auss6,$pal,$kzgut) = $idGutAussArray;

			$sql="update dauftr set `auftragsnr-exp`='$export',`pal-nr-exp`='$pal',`stk-exp`='$gut',auss2_stk_exp='$auss2',auss4_stk_exp='$auss4',auss6_stk_exp='$auss6' where (id_dauftr='$id') limit 1";
			$output.='<idrow>';
			$output.="<id>$id</id>";
			$output.="<gut>$gut</gut>";
			$output.="<auss2>$auss2</auss2>";
			$output.="<auss4>$auss4</auss4>";
			$output.="<auss6>$auss6</auss6>";
			$output.="<pal>$pal</pal>";
			$output.="<kzgut>$kzgut</kzgut>";
			$output.="<sql>$sql</sql>";
			mysql_query($sql);
			$mysqlerror=mysql_error();
			$output.="<mysqlerror>chyba:$mysqlerror</mysqlerror>";
			$output.='</idrow>';

			//dalsi prvky budou pridany do rootu dokumentu
			// zapisy do skladu
			// jen v pripade, ze mam polozku s G
			
			if($kzgut=='G')
			{
			    //vyber z versandlageru
			    $palStr = strval($pal);
//			    $dil = getTeilFromAuftragPal($import,$pal);
			    $dauftrRow = $a->getDauftrRow($id);
			    $auftragsnr = $dauftrRow['auftragsnr'];
			    $dil = $dauftrRow['teil'];
			    if (substr($palStr, strlen($palStr) - 1) == "7") {
				$sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
				$sql_insert.= "values ('$dil','$auftragsnr','$pal','$gut',0,'8V','9V','$ident')";
				mysql_query($sql_insert);
			    }
			    //------------------------------------------------------------------
			    
				$lVon = '8E';
				$lNach = '8X';
				// pokud mam nenulovy dobrych pocet kusu ve skladu 8X, budu stornovat posledni exportni pozici
				$pocetKusu = getLagerGutAuftragPalette($auftragsnr,$pal,$lNach);
				
				if($pocetKusu>0)
				{
					// vystornuju posledni exportni zaznam
					$sql = "select gut_stk from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (lager_von='$lVon') and (lager_nach='$lNach')) order by date_stamp desc limit 1";
					$res = mysql_query($sql);

					if(mysql_affected_rows()>0)
					{
						$row = mysql_fetch_array($res);
						$stornoStk = $row['gut_stk'];
						// 	potrebuju znat cislo dilu, ktery na v dane zakazce na dane palete
//						$dil = getTeilFromAuftragPal($import,$pal);
						insertLagerVonNach($dil,$auftragsnr,$pal,$stornoStk,0,$lNach,$lVon,$ident);						
					}
				}
				
				// to same pro sklad XX
				$pocetKusu = getLagerGutAuftragPalette($auftragsnr,$pal,"XX");
				
				if($pocetKusu>0)
				{
					// vystornuju posledni exportni zaznam
					$sql = "select lager_von,gut_stk from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (lager_nach='XX')) order by date_stamp desc limit 1";
					$res = mysql_query($sql);

					if(mysql_affected_rows()>0)
					{
						$row = mysql_fetch_array($res);
						$stornoStk = $row['gut_stk'];
						$lVon = $row['lager_von'];
						// 	potrebuju znat cislo dilu, ktery na v dane zakazce na dane palete
//						$dil = getTeilFromAuftragPal($import,$pal);
						insertLagerVonNach($dil,$auftragsnr,$pal,$stornoStk,0,"XX",$lVon,$ident);						
					}
				}
				
				// a jeste jednou pro vyexportovane ausschussy (B2,B4,B6)
				$aussTypenB = array("B2","B4","B6");
				$aussTypenA = array("A2","A4","A6");
				$poradiAussTyp=0;
				foreach($aussTypenB as $aussTyp)
				{
					if(getLagerAussAuftragPalette($auftragsnr,$pal,$aussTyp)!=0)
					{
						$lVon = $aussTypenA[$poradiAussTyp];
						$lNach = $aussTyp;
						$sql = "select lager_nach,lager_von,auss_stk from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (lager_von='$lVon') and (lager_nach='$lNach')) order by date_stamp desc";
						$res = mysql_query($sql);
						if(mysql_affected_rows()>0)
						{
							$row = mysql_fetch_array($res);
							$lNach = $row['lager_nach'];
							$lVon = $row['lager_von'];
							$aussStk = $row['auss_stk'];
//							$dil = getTeilFromAuftragPal($import,$pal);
							insertLagerVonNach($dil,$auftragsnr,$pal,0,$aussStk,$lNach,$lVon,$ident);
						}
					}
					$poradiAussTyp++;
				}
				
				// a nakonec vlastni presun do skladu pro export 8E->8X
//				$dil = getTeilFromAuftragPal($import,$pal);
				insertLagerVonNach($dil,$auftragsnr,$pal,$gut,0,"8E","8X",$ident);
				
				// presun do dummy lagru, aby mi nezbyvalo v prvnim skladu
				// jmeno prvniho skladu
				$eL = erster_lager($dil,$auftragsnr,$pal);
				// kolik kusu zbyva v prvnim skladu
				$pocetKusuVlozenych = getLagerGutIn($auftragsnr,$pal,$eL);
				$pocetKusuOdebranych = getLagerGesamtOut($auftragsnr,$pal,$eL);
				$zbyvaKusu = $pocetKusuVlozenych - $pocetKusuOdebranych;

				if($zbyvaKusu!=0)	insertLagerVonNach($dil,$auftragsnr,$pal,$zbyvaKusu,0,$eL,"XX",$ident);
				
				// presun zmetku ve vyrobe do zmetku vyexportovanych, pocty si beruz tabulky drueck
				$auss2 = getAussFromDrueckAuftragPalTyp($auftragsnr,$pal,2);
				$auss4 = getAussFromDrueckAuftragPalTyp($auftragsnr,$pal,4);
				$auss6 = getAussFromDrueckAuftragPalTyp($auftragsnr,$pal,6);
				if($auss2!=0) moveAussLagerFromA2B($auftragsnr,$dil,$pal,$auss2,"A2","B2");
				if($auss4!=0) moveAussLagerFromA2B($auftragsnr,$dil,$pal,$auss4,"A4","B4");
				if($auss6!=0) moveAussLagerFromA2B($auftragsnr,$dil,$pal,$auss6,"A6","B6");
                
			}
			
		}
	}
	else
	{
		$output.="<error>zadana zakazka pro export neexistuje nebo jiz byla vyfakturovana</error>";
	}
	$output .= '</response>';
	
	echo $output;
	
?>

