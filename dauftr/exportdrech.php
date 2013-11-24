<?
session_start();
require "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$auftragsnr = $_GET['auftragsnr'];
	$run = $_GET['run'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	// vratim cislo behu
	$nodeElement = $doc->createElement("run");
	$nodeText = $doc->createTextNode($run);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	// vratim cislo faktury
	$nodeElement = $doc->createElement("auftragsnr");
	$nodeText = $doc->createTextNode($auftragsnr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	// pokud je run=1 mam prvni beh, testnu, zda uz data nejsou v tabulce drechbew pritomna
	if($run==1)
	{
		$exportiert=drechExportiert($auftragsnr);
		$nodeElement = $doc->createElement("exportiert");
		$nodeText = $doc->createTextNode($exportiert);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
	}
	else
	{
		// nejdriv zlikviduju stare zaznamy
		mysql_query("delete from drechbew where (auftragsnr='$auftragsnr')");
		// zkopiruju vybrany obsah z drech do drechbew
		// vybrat vsechny radky z drech
		$sql_select = "select * from drech where (auftragsnr='$auftragsnr')";
		$res = mysql_query($sql_select);
		if(mysql_affected_rows()>0)
		{
			$positionen=0;
			$hodnota=0;
			$positionentable = "<table border='1' cellspacing='0' cellpadding='2' >";
				$positionentable.="<tr>";
				$positionentable.="<td>teil</td>";
				$positionentable.="<td>pal</td>";
				$positionentable.="<td>taetigkeit</td>";
				$positionentable.="<td>gutstk</td>";
				$positionentable.="<td>ausschuss</td>";
				$positionentable.="<td>preis</td>";
				$positionentable.="</tr>";
			
			while($drechrow = mysql_fetch_array($res))
			{
				
				
				if(strlen($drechrow['fremdauftr'])==0)
					$fremdauftr='null';
				else
					$fremdauftr="'".$drechrow['fremdauftr']."'";
					
				if(strlen($drechrow['fremdpos'])==0)
					$fremdpos='null';
				else
					$fremdpos="'".$drechrow['fremdpos']."'";

				$positionentable.="<tr>";
				$sql_insert = "insert into drechbew";
				$sql_insert.= " (AuftragsNr,Teil,`Stück`,Ausschuss,DM,`DM-Mehr`,Datum,Text1,Text2,`Taet-kz`,`Best-Nr`,`datum-auslief`,`pos-pal-nr`,fremdauftr,fremdpos,teilbez,kunde,abgnr) ";
				$sql_insert.= " values (".
				"'".$drechrow['AuftragsNr']."','".
				$drechrow['Teil']."','".
				$drechrow['Stück']."','".
				$drechrow['Ausschuss']."','".
				$drechrow['DM']."','".
				$drechrow['DM-Mehr']."','".
				$drechrow['Datum']."','".
				$drechrow['Text1']."','".
				$drechrow['Text2']."','".
				$drechrow['Taet-kz']."','".
				$drechrow['Best-Nr']."','".
				$drechrow['datum-auslief']."','".
				$drechrow['pos-pal-nr']."',".
				$fremdauftr.",".
				$fremdpos.",'".
                mysql_real_escape_string($drechrow['teilbez'])."','".
				$drechrow['kunde']."','".
				$drechrow['abgnr']."')";
				mysql_query($sql_insert);
				
				$positionentable.="<td align='left'>".$drechrow['Teil']."</td>";
				$positionentable.="<td align='right'>".$drechrow['pos-pal-nr']."</td>";
				$positionentable.="<td align='left'>".$drechrow['Text1']."</td>";
				$positionentable.="<td align='right'>".$drechrow['Stück']."</td>";
				$positionentable.="<td align='right'>".$drechrow['Ausschuss']."</td>";
				$positionentable.="<td align='right'>".$drechrow['DM']."</td>";
				
				if(mysql_affected_rows()>0)
				{
					$positionen++;
					$hodnota+= ($drechrow['Stück']+$drechrow['Ausschuss'])*$drechrow['DM'];
				}
				$nodeElement = $doc->createElement("sqlinsert");
				$nodeText = $doc->createTextNode($sql_insert);
				$nodeElement->appendChild($nodeText);
				$root->appendChild($nodeElement);
				$positionentable.="<tr>";
			}
			
			$positionentable.="</table>";
		}
		// poslat informacni email
		
		//$recipient = "jr@abydos.cz,";
		//$recipient.= "pepa@runtici.cz,";
                $recipient = "hl@abydos.cz,";
                $recipient .= "sz@abydos.cz";
		
		// posle mail i odesilateli
//		$uzivatel = get_user();
//		if(strlen($uzivatel)>0)
//		{
//		 $uzivatelemail = $uzivatel."@abydos.cz";
//		 $recipient.= ",$uzivatelemail";
//		}
		
		
		
		$subject = "Rechnung ".$auftragsnr;
		$message = "<h3>Daten fur Rechnung <b>$auftragsnr</b> sind vorbereitet.</h3>";
		

		$hodnota=round($hodnota,2);


		$message.="<table border='1' cellspacing='0' cellpadding='2' >";
		$message.="<tr>";
		$message.="<td width='200' bgcolor='lightyellow'>Positionen</td>";
		$message.="<td width='200' align='right'><b>$positionen</b></td>";
		$message.="</tr>";
		$message.="<tr>";
		$message.="<td bgcolor='lightblue'>Wert</td>";
		$message.="<td align='right'><b>$hodnota</b></td>";
		$message.="</tr>";
		$message.="</table>";
		
		$user = get_user_pc();
		$message.= "<br><br>mit freundlichen Gruessen<br>$user";
		
		$message.=$positionentable;
		
		$headers = "From: <apl@abydos.cz>\n";
		$headers = "Content-Type: text/html; charset=UTF-8\n";
		
		@mail($recipient,$subject,$message,$headers);
	}
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

