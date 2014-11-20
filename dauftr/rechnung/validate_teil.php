<?
require_once '../../security.php';
require "../../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	$kunde = $_GET['kunde'];
	$auftragsnr = $_GET['auftragsnr'];


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');

	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	

    /////////////////////////////////////////////////////////////////////////////////////////
	
	// 1. zjistim si cenu za minutu pro aktualniho zakaznika
	$minpreis=$_GET['minpreis'];
	
	// 2. zjistim si podle cisla zakaznika na kolik desetinnych mist mam zaokrouhlovat
	$sql = "SELECT dksd.preis_runden as rn FROM DKSD where (kunde=".$kunde.")";
	$r=mysql_query($sql);
	$row=mysql_fetch_array($r);
	$runden=$row['rn'];

	$sql="select daufkopf.auftragsnr,fremdauftr,fremdpos from dauftr join daufkopf using(auftragsnr)";
	$sql.="	where ((fremdauftr>0) and (teil='$value')) order by daufkopf.aufdat desc limit 1";
	$result=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($result);
		$fremdausauftrag = $row['auftragsnr'];
		$fremdauftr=$row['fremdauftr'];
		$fremdpos=$row['fremdpos'];
	}
	else
	{
		$fremdausauftrag = '';
		$fremdauftr='';
		$fremdpos='';
	}

	$output.="<fremdausauftrag>$fremdausauftrag</fremdausauftrag>";
	$output.="<fremdauftrag>$fremdauftr</fremdauftrag>";
	$output.="<fremdpos>$fremdpos</fremdpos>";
	$output.="<auftragsnr>$auftragsnr</auftragsnr>";
	$output.="<kunde>$kunde</kunde>";
	$output.="<minpreis>$minpreis</minpreis>";

	// TODO
	// dodelat upravu , pokud uz je v zakazce u daneho dilu naplanovan termin, tak ho predvyplnit do formulare
	//
	//
	// 3. vytahnu informace z pracovniho planu pro dany dil
	$sql="select `TaetNr-Aby` as abgnr,dtaetkz as tat,`VZ-min-kunde` as vzkd, `vz-min-aby` as vzaby,";
	$sql.="KzGut as kzgut,`kz-druck` as kzdruck";
	$sql.=" from dpos join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby` ";
	$sql.=" where ((dpos.teil='".$value."') and (dpos.`TaetNr-Aby`>3)) order by abgnr";
	$result=mysql_query($sql);
	$sqlerror=mysql_error();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output .= '<teil>' . $value . '</teil>';
			$output .= '<abgnr>' . $row['abgnr'] . '</abgnr>';
			$output .= '<tat>' . $row['tat'] . '</tat>';
			$output .= '<vzkd>' . $row['vzkd'] . '</vzkd>';
			$output .= '<preis>' . round($row['vzkd']*$minpreis,$runden) . '</preis>';
			$output .= '<vzaby>' . $row['vzaby'] . '</vzaby>';
			$output .= '<kzgut>' . $row['kzgut'] . '</kzgut>';
			$output .= '<kzdruck>' . $row['kzdruck'] . '</kzdruck>';
			$output .= '<minpreis>' . $minpreis . '</minpreis>';
			$output .= '<runden>' . $runden . '</runden>';
		}
	}
	else
	{
		$output.="<teile>";
		$output .= '<teil>' . "ERROR" . '</teil>';
		$output .= '<errordescription>apl has no positions</errordescription>';
		$output .= '<sql>' . $sql . '</sql>';
		$output .= '<sqlerror>' . $sqlerror . '</sqlerror>';
		$output.="</teile>";
	}
	
	// 4. zjistit posledni obsah fremdauftr a fremdpos pro tento dil
	// tento dotaz celkem zdrzuje = TODO zoptimalizovat
	$sql = "SELECT DAUFTR.fremdauftr, DAUFTR.fremdpos FROM DAUFTR INNER JOIN DAufKopf ON DAUFTR.AuftragsNr = DAufKopf.AuftragsNr where (((DAUFTR.Teil) = ".$value.") And ((DAUFTR.fremdauftr) Is Not Null And (DAUFTR.fremdauftr) > '0')) ORDER BY DAufKopf.Aufdat DESC limit 1;";
	//$result=mysql_query($sql);
	$sqlerror=mysql_error();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output .= '<fremdauftr>' . $row['fremdauftr'] . '</fremdauftr>';
			$output .= '<fremdpos>' . $row['fremdpos'] . '</fremdpos>';
		}
	}
	else
	{
		$output.="<teile>";
		$output .= '<teil>' . "ERROR-FREMD" . '</teil>';
		$output .= '<sql>' . $sql . '</sql>';
		$output .= '<sqlerror>' . $sqlerror . '</sqlerror>';
		$output .= '<errordescription>fremd error</errordescription>';
		$output.="</teile>";
	}

	// 5. zjistim, zda zadany dil uz neni v zakazce a ma vyplneny termin
	$sql = "select termin from dauftr where (((DAUFTR.Teil) = ".$value.") And ((DAUFTR.termin) Is Not Null) and (dauftr.auftragsnr=".$auftragsnr.")) order by dauftr.termin desc";
	$result=mysql_query($sql);
	$sqlerror=mysql_error();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output .= '<termin>' . $row['termin'] . '</termin>';
		}
	}
	else
	{
		$output.="<teile>";
		$output .= '<teil>' . "ERROR-TERMIN" . '</teil>';
		$output .= '<sql>' . $sql . '</sql>';
		$output .= '<sqlerror>' . $sqlerror . '</sqlerror>';
		$output .= '<errordescription>notermin</errordescription>';
		$output .= '<auftragsnr>' . $auftragsnr . '</auftragsnr>';
		$output.="</teile>";
	}

	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>
