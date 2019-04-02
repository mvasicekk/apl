<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = trim($_GET['value']);
	$auftragsnr=$_GET['auftragsnr_value'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	// zjistim zda uz je paleta exportovana
	$hasExport=1;
	
	if(strlen($value)>0)
		$hasExport = hasExport($auftragsnr,$value);

//        $hasExport = 0;
        
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<hasexport>' . $hasExport . '</hasexport>';
	
        
	
	// 1. zjistim existenci palety v zakazce
	$sql = "select dauftr.teil,teilbez from dauftr join dkopf using(teil) where ((auftragsnr='$auftragsnr') and (dauftr.`pos-pal-nr`='$value'))";
	mysql_query('set names utf8');
	$resTeil = mysql_query($sql);
	if((mysql_affected_rows()>0)&&(strlen($value)>0))
	{
		// takova paleta v zakazce existuje
		$row=mysql_fetch_array($resTeil);
		$output .= "<teil>".$row['teil']."</teil>";
		$output .= "<pal>".$value."</pal>";
		$output .= "<teilbez>".$row['teilbez']."</teilbez>";
		
		// ted vytahnu operace pro danou paletu a dil
		// nema export
		if($hasExport==0)
		{
		    //2018-12-18
			//$sql="select abgnr,dauftr.teil,teilbez from dauftr join dkopf on dkopf.teil=dauftr.teil where ((`pos-pal-nr`='".$value."') and (auftragsnr='".$auftragsnr."')) order by abgnr";
			$sql="select abgnr,dauftr.teil,teilbez from dauftr join dkopf on dkopf.teil=dauftr.teil where ((`pos-pal-nr`='".$value."') and (auftragsnr='".$auftragsnr."') and kz_aktiv<>0) order by abgnr";
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
				$output .= "<errordescription>ERROR-PAL-NOTAT</errordescription>";
				$output .= "<sql>$sql</sql>";
				$output.="</error>";
			}
		}
		else
		{
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
				$output .= "<errordescription>ERROR-PAL-NOINTTAT</errordescription>";
				$output .= "<sql>$sql</sql>";
				$output.="</error>";
				
			}
		}
	}
	else
	{
		// takovou paletu v zakazce nemam
			$output.="<error>";
			$output .= "<errordescription>ERROR-NOPAL</errordescription>";
			$output .= "<sql>$sql</sql>";
			$output.="</error>";
	}
	
		
	$output.="<controlid>";
	$output .= $controlid;
	$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

