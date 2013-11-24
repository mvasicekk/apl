<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	$teil=$_GET['teil_value'];
	$teilbez=$_GET['teilbez_value'];
	$auftragsnr=$_GET['auftragsnr_value'];
	$pal=$_GET['pal_value'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	// zjistim zda uz je paleta exportovana
	$hasExport = hasExport($auftragsnr,$pal);
//        $hasExport=0;

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<hasexport>' . $hasExport . '</hasexport>';

	// informace o dilu 
	$output .= "<teil>".$teil."</teil>";
	$output .= "<pal>".$pal."</pal>";
	$output .= "<teilbez>".$teilbez."</teilbez>";
	
	if($hasExport)
	{
		// mam export u palety	
		if($value>0)
		{
			// chci zadavat vicepraci
			// paleta uz je vyexportovana a nabidnu pro dany dil jen interni operace
			// vytahnu vsechny interni operace a rozlisim podle zakazky
			if($auftragsnr<999999)
				$sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`<7000)) order by abgnr";
			else
				$sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`>6999)) order by abgnr";
			$result_operace = mysql_query($sql);
			if(mysql_affected_rows()>0)
			{
				$output.="<taetigkeiten>";
				while($row = mysql_fetch_array($result_operace))	$output .= '<abgnr>' . $row['abgnr'] . '</abgnr>';
				$output.="</taetigkeiten>";
			}
			else
			{
				// nemam zadne interni operace ? dost nepravdepodobny, ale stat se muze
				$output.="<error>";
				$output .= "<errordescription>ERROR-MEHR-NOINTTAT</errordescription>";
				$output .= "<sql>$sql</sql>";
				$output.="</error>";
			}			
		}
		else
		{
			// chci zadavat operace z dauftr, ale to u exportovane palety nesmim
			$output.="<error>";
			$output .= "<errordescription>ERROR-MEHR-EXPORTEDPAL</errordescription>";
			$output .= "<sql>$sql</sql>";
			$output.="</error>";
		}
	}
	else
	{
		// nemam export
		if($value>0)
		{
			// chci zadat vicepraci
			// vytahnu vsechny interni operace a rozlisim podle zakazky
			if($auftragsnr<999999)
				$sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`<7000)) order by abgnr";
			else
				$sql="select `abg-nr` as abgnr from `dtaetkz-abg` where ((dtaetkz='I') and (`abg-nr`<>'3') and (`abg-nr`>6999)) order by abgnr";
			$result_operace = mysql_query($sql);
			if(mysql_affected_rows()>0)
			{
				$output.="<taetigkeiten>";
				while($row = mysql_fetch_array($result_operace))	$output .= '<abgnr>' . $row['abgnr'] . '</abgnr>';
				$output.="</taetigkeiten>";
			}
			else
			{
				// nemam zadne interni operace ? dost nepravdepodobny, ale stat se muze
				$output.="<error>";
				$output .= "<errordescription>ERROR-MEHR-NOINTTAT</errordescription>";
				$output .= "<sql>$sql</sql>";
				$output.="</error>";
			}			
		}
		else
		{
			// budu zadavat operace z dauftr
			$sql="select abgnr,dauftr.teil,teilbez from dauftr join dkopf on dkopf.teil=dauftr.teil where ((`pos-pal-nr`='".$pal."') and (auftragsnr='".$auftragsnr."')) order by abgnr";
			$result=mysql_query($sql);
			// pokud mi dotaz vrati nejake zaznamy, tak je projdu
			if(mysql_affected_rows()>0)
			{
				$output.="<taetigkeiten>";
				while ($row = mysql_fetch_array($result))	$output .= '<abgnr>' . $row['abgnr'] . '</abgnr>';
				$output.="</taetigkeiten>";
				
			}
			else
			{
				// pro zadany dil nemam v dauftr zadne operace
				$output.="<error>";
				$output .= "<errordescription>ERROR-MEHR-NODAUFTRTAT</errordescription>";
				$output .= "<sql>$sql</sql>";
				$output.="</error>";
			}
		}
	}

	$output.="<controlid>";
	$output .= $controlid;
	$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

